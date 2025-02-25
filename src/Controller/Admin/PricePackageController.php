<?php

// src/Controller/Admin/PricePackageController.php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Common\DoctrineListRepresentationFactory;
use App\Entity\PricePackage;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sulu\Bundle\MediaBundle\Entity\MediaInterface;
use Sulu\Bundle\MediaBundle\Media\Manager\MediaManagerInterface;
use Sulu\Component\Rest\ListBuilder\Doctrine\DoctrineListBuilderFactoryInterface;
use Sulu\Component\Rest\ListBuilder\Metadata\FieldDescriptorFactoryInterface;
use Sulu\Component\Rest\ListBuilder\PaginatedRepresentation;
use Sulu\Component\Rest\RestHelperInterface;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @phpstan-type PricePackageData array{
 *     id: int|null,
 *     title: string,
 *     image: array{id: int}|null,
 *     tracklist: mixed[],
 *     bulletPoints: mixed[],
 * }
 */
class PricePackageController extends AbstractFOSRestController implements SecuredControllerInterface
{
    protected $doctrineListRepresentationFactory;
    protected $entityManager;
    protected $mediaManager;
    protected $viewHandler;
    protected $fieldDescriptorFactory;
    protected $listBuilderFactory;
    protected $restHelper;

    public function __construct(
        DoctrineListRepresentationFactory $doctrineListRepresentationFactory,
        EntityManagerInterface $entityManager,
        MediaManagerInterface $mediaManager,
        ViewHandlerInterface $viewHandler,
        FieldDescriptorFactoryInterface $fieldDescriptorFactory,
        DoctrineListBuilderFactoryInterface $listBuilderFactory,
        RestHelperInterface $restHelper
    ) {
        $this->doctrineListRepresentationFactory = $doctrineListRepresentationFactory;
        $this->entityManager = $entityManager;
        $this->mediaManager = $mediaManager;
        $this->viewHandler = $viewHandler;
        $this->fieldDescriptorFactory = $fieldDescriptorFactory;
        $this->listBuilderFactory = $listBuilderFactory;
        $this->restHelper = $restHelper;
    }

    #[Route(path: '/{id}', methods: ['GET'], name: 'app.price_packages.get_price_package')]
    public function getAction(int $id): Response
    {
        $pricePackage = $this->entityManager->getRepository(PricePackage::class)->find($id);
        if (!$pricePackage instanceof PricePackage) {
            throw new NotFoundHttpException();
        }

        $view = View::create($this->getDataForEntity($pricePackage));
        $view->setFormat('json');
        return $this->viewHandler->handle($view);
    }

    #[Route(path: '/{id}', methods: ['PUT'], name: 'app.price_packages.put_price_package')]
    public function putAction(Request $request, int $id): Response
    {
        $pricePackage = $this->entityManager->getRepository(PricePackage::class)->find($id);
        if (!$pricePackage instanceof PricePackage) {
            throw new NotFoundHttpException();
        }

        /** @var PricePackageData $data */
        $data = $request->toArray();
        $this->mapDataToEntity($data, $pricePackage);
        $this->entityManager->flush();

        $view = View::create($this->getDataForEntity($pricePackage));
        $view->setFormat('json');
        return $this->viewHandler->handle($view);
    }

    #[Route(path: '', methods: ['POST'], name: 'app.price_packages.post_price_package')]
    public function postAction(Request $request): Response
    {
        $pricePackage = new PricePackage();
        /** @var PricePackageData $data */
        $data = $request->toArray();
        $this->mapDataToEntity($data, $pricePackage);
        $this->entityManager->persist($pricePackage);
        $this->entityManager->flush();

        $view = View::create($this->getDataForEntity($pricePackage));
        $view->setFormat('json');
        $view->setStatusCode(201);
        return $this->viewHandler->handle($view);
    }

    #[Route(path: '/{id}', methods: ['DELETE'], name: 'app.price_packages.delete_price_package')]
    public function deleteAction(int $id): Response
    {
        /** @var PricePackage $pricePackage */
        $pricePackage = $this->entityManager->getReference(PricePackage::class, $id);
        $this->entityManager->remove($pricePackage);
        $this->entityManager->flush();

        $view = View::create(null);
        $view->setFormat('json');
        $view->setStatusCode(204);
        return $this->viewHandler->handle($view);
    }

    #[Route(path: '', methods: ['GET'], name: 'app.price_packages.cget_price_package')]
    public function cgetAction(Request $request): Response
    {
        try {
            $fieldDescriptors = $this->fieldDescriptorFactory->getFieldDescriptors(PricePackage::RESOURCE_KEY);
            $listBuilder = $this->listBuilderFactory->create(PricePackage::class);
            $this->restHelper->initializeListBuilder($listBuilder, $fieldDescriptors);

            $listRepresentation = new PaginatedRepresentation(
                $listBuilder->execute(),
                PricePackage::RESOURCE_KEY,
                (int) $listBuilder->getCurrentPage(),
                (int) $listBuilder->getLimit(),
                (int) $listBuilder->count()
            );

            $view = View::create($listRepresentation);
            $view->setFormat('json');
            return $this->viewHandler->handle($view);
        } catch (\Exception $e) {
            // Für Debugging-Zwecke
            return new Response('Error: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @return PricePackageData
     */
    protected function getDataForEntity(PricePackage $entity): array
    {
        $image = $entity->getImage();

        return [
            'id' => $entity->getId(),
            'title' => $entity->getTitle(),
            'image' => $image instanceof MediaInterface
                ? ['id' => $image->getId()]
                : null,
            'tracklist' => $entity->getTracklist(),
            'bulletPoints' => $entity->getBulletPoints(),
        ];
    }

    /**
     * @param PricePackageData $data
     */
    protected function mapDataToEntity(array $data, PricePackage $entity): void
    {
        $imageId = isset($data['image']) && isset($data['image']['id']) ? $data['image']['id'] : null;

        $entity->setTitle($data['title']);
        $entity->setImage($imageId ? $this->mediaManager->getEntityById($imageId) : null);
        $entity->setTracklist($data['tracklist'] ?? []);
        $entity->setBulletPoints($data['bulletPoints'] ?? []);
    }

    public function getSecurityContext(): string
    {
        return PricePackage::SECURITY_CONTEXT;
    }

    public function getLocale(Request $request): ?string
    {
        return $request->query->get('locale');
    }
}