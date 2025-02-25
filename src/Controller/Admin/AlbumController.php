<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Common\DoctrineListRepresentationFactory;
use App\Entity\Album;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Bundle\MediaBundle\Entity\MediaInterface;
use Sulu\Bundle\MediaBundle\Media\Manager\MediaManagerInterface;
use Sulu\Component\Security\SecuredControllerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sulu\Component\Rest\ListBuilder\Doctrine\DoctrineListBuilderFactoryInterface;
use Sulu\Component\Rest\ListBuilder\Metadata\FieldDescriptorFactoryInterface;
use Sulu\Component\Rest\ListBuilder\PaginatedRepresentation;
use Sulu\Component\Rest\RestHelperInterface;

/**
 * @phpstan-type AlbumData array{
 *     id: int|null,
 *     title: string,
 *     image: array{id: int}|null,
 *     tracklist: mixed[],
 * }
 */
/**
 * @RouteResource("album")
 */
class AlbumController extends AbstractController implements SecuredControllerInterface
{
    protected $doctrineListRepresentationFactory;
    protected $entityManager;
    protected $mediaManager;

    public function __construct(
        DoctrineListRepresentationFactory $doctrineListRepresentationFactory,
        EntityManagerInterface $entityManager,
        MediaManagerInterface $mediaManager,
        private ViewHandlerInterface $viewHandler,
        private FieldDescriptorFactoryInterface $fieldDescriptorFactory,
        private DoctrineListBuilderFactoryInterface $listBuilderFactory,
        private RestHelperInterface $restHelper
    ) {
        $this->doctrineListRepresentationFactory = $doctrineListRepresentationFactory;
        $this->entityManager = $entityManager;
        $this->mediaManager = $mediaManager;
    }

    #[Route(path: '/admin/api/albums/{id}', methods: ['GET'], name: 'app.get_album')]
    public function get(int $id): Response
    {
        $album = $this->entityManager->getRepository(Album::class)->find($id);
        if (!$album instanceof Album) {
            throw new NotFoundHttpException();
        }

        return $this->json($this->getDataForEntity($album));
    }

    #[Route(path: '/admin/api/albums/{id}', methods: ['PUT'], name: 'app.put_album')]
    public function put(Request $request, int $id): Response
    {
        $album = $this->entityManager->getRepository(Album::class)->find($id);
        if (!$album instanceof Album) {
            throw new NotFoundHttpException();
        }
        /** @var AlbumData $data */
        $data = $request->toArray();
        $this->mapDataToEntity($data, $album);
        $this->entityManager->flush();

        return $this->json($this->getDataForEntity($album));
    }

    #[Route(path: '/admin/api/albums', methods: ['POST'], name: 'app.post_album')]
    public function post(Request $request): Response
    {
        $album = new Album();
        /** @var AlbumData $data */
        $data = $request->toArray();
        $this->mapDataToEntity($data, $album);
        $this->entityManager->persist($album);
        $this->entityManager->flush();

        return $this->json($this->getDataForEntity($album), 201);
    }

    #[Route(path: '/admin/api/albums/{id}', methods: ['DELETE'], name: 'app.delete_album')]
    public function delete(int $id): Response
    {
        /** @var Album $album */
        $album = $this->entityManager->getReference(Album::class, $id);
        $this->entityManager->remove($album);
        $this->entityManager->flush();

        return $this->json(null, 204);
    }

    #[Route(path: '/admin/api/albums', methods: ['GET'], name: 'app.get_album_list')]
    public function getList(): Response
    {
        $fieldDescriptors = $this->fieldDescriptorFactory->getFieldDescriptors(Album::RESOURCE_KEY);
        $listBuilder = $this->listBuilderFactory->create(Album::class);
        $this->restHelper->initializeListBuilder($listBuilder, $fieldDescriptors);

        $listRepresentation = new PaginatedRepresentation(
            $listBuilder->execute(),
            Album::RESOURCE_KEY,
            $listBuilder->getCurrentPage(),
            $listBuilder->getLimit(),
            $listBuilder->count()
        );

        return $this->viewHandler->handle(View::create($listRepresentation));
    }

    /**
     * @return AlbumData $data
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
            'tracklist' => $entity->getTracklist(),
        ];
    }

    /**
     * @param AlbumData $data
     */
    protected function mapDataToEntity(array $data, Album $entity): void
    {
        $imageId = $data['image']['id'] ?? null;

        $entity->setTitle($data['title']);
        $entity->setImage($imageId ? $this->mediaManager->getEntityById($imageId) : null);
        $entity->setTracklist($data['tracklist']);
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
