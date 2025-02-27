<?php

// src/Controller/Admin/AlbumController.php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Common\DoctrineListRepresentationFactory;
use App\Entity\Album;
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
 * @phpstan-type AlbumData array{
 *     id: int|null,
 *     title: string,
 *     image: array{id: int}|null,
 * }
 */
class AlbumController extends AbstractFOSRestController implements SecuredControllerInterface
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

    #[Route(path: '/admin/api/albums/{id}', methods: ['GET'], name: 'app.albums.get_album')]
    public function getAction(int $id): Response
    {
        $album = $this->entityManager->getRepository(Album::class)->find($id);
        if (!$album instanceof Album) {
            throw new NotFoundHttpException();
        }

        $view = View::create($this->getDataForEntity($album));
        $view->setFormat('json');
        return $this->viewHandler->handle($view);
    }

    #[Route(path: '/admin/api/albums/{id}', methods: ['PUT'], name: 'app.albums.put_album')]
    public function putAction(Request $request, int $id): Response
    {
        $album = $this->entityManager->getRepository(Album::class)->find($id);
        if (!$album instanceof Album) {
            throw new NotFoundHttpException();
        }

        /** @var AlbumData $data */
        $data = $request->toArray();
        $this->mapDataToEntity($data, $album);
        $this->entityManager->flush();

        $view = View::create($this->getDataForEntity($album));
        $view->setFormat('json');
        return $this->viewHandler->handle($view);
    }

    #[Route(path: '/admin/api/albums', methods: ['POST'], name: 'app.albums.post_album')]
    public function postAction(Request $request): Response
    {
        $album = new Album();
        /** @var AlbumData $data */
        $data = $request->toArray();
        $this->mapDataToEntity($data, $album);
        $this->entityManager->persist($album);
        $this->entityManager->flush();

        $view = View::create($this->getDataForEntity($album));
        $view->setFormat('json');
        $view->setStatusCode(201);
        return $this->viewHandler->handle($view);
    }

    #[Route(path: '/admin/api/albums/{id}', methods: ['DELETE'], name: 'app.albums.delete_album')]
    public function deleteAction(int $id): Response
    {
        /** @var Album $album */
        $album = $this->entityManager->getReference(Album::class, $id);
        $this->entityManager->remove($album);
        $this->entityManager->flush();

        $view = View::create(null);
        $view->setFormat('json');
        $view->setStatusCode(204);
        return $this->viewHandler->handle($view);
    }

    #[Route(path: '/admin/api/albums', methods: ['GET'], name: 'app.albums.cget_album')]
    public function cgetAction(Request $request): Response
    {
        $fieldDescriptors = $this->fieldDescriptorFactory->getFieldDescriptors(Album::RESOURCE_KEY);
        $listBuilder = $this->listBuilderFactory->create(Album::class);
        $this->restHelper->initializeListBuilder($listBuilder, $fieldDescriptors);

        $listRepresentation = new PaginatedRepresentation(
            $listBuilder->execute(),
            Album::RESOURCE_KEY,
            (int) $listBuilder->getCurrentPage(),
            (int) $listBuilder->getLimit(),
            (int) $listBuilder->count()
        );

        $view = View::create($listRepresentation);
        $view->setFormat('json');
        return $this->viewHandler->handle($view);
    }

    /**
     * @return AlbumData
     */
    protected function getDataForEntity(Album $entity): array
    {
        $image = $entity->getImage();

        return [
            'id' => $entity->getId(),
            'title' => $entity->getTitle(),
            'image' => $image instanceof MediaInterface
                ? ['id' => $image->getId()]
                : null,
        ];
    }

    /**
     * @param AlbumData $data
     */
    protected function mapDataToEntity(array $data, Album $entity): void
    {
        $imageId = isset($data['image']) && isset($data['image']['id']) ? $data['image']['id'] : null;

        $entity->setTitle($data['title']);
        $entity->setImage($imageId ? $this->mediaManager->getEntityById($imageId) : null);
    }

    public function getSecurityContext(): string
    {
        return Album::SECURITY_CONTEXT;
    }

    public function getLocale(Request $request): ?string
    {
        return $request->query->get('locale');
    }
}