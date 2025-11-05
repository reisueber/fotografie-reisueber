<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Common\DoctrineListRepresentationFactory;
use App\Entity\BlogPost;
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
 * @phpstan-type BlogPostData array{
 *     id: int|null,
 *     title: string,
 *     slug: string,
 *     teaser: string|null,
 *     content: string|null,
 *     image: array{id: int}|null,
 *     publishedAt: string|null,
 * }
 */
class BlogPostController extends AbstractFOSRestController implements SecuredControllerInterface
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

    #[Route(path: '/admin/api/blog-posts/{id}', methods: ['GET'], name: 'app.blog_posts.get_blog_post')]
    public function getAction(int $id): Response
    {
        $blogPost = $this->entityManager->getRepository(BlogPost::class)->find($id);
        if (!$blogPost instanceof BlogPost) {
            throw new NotFoundHttpException();
        }

        $view = View::create($this->getDataForEntity($blogPost));
        $view->setFormat('json');
        return $this->viewHandler->handle($view);
    }

    #[Route(path: '/admin/api/blog-posts/{id}', methods: ['PUT'], name: 'app.blog_posts.put_blog_post')]
    public function putAction(Request $request, int $id): Response
    {
        $blogPost = $this->entityManager->getRepository(BlogPost::class)->find($id);
        if (!$blogPost instanceof BlogPost) {
            throw new NotFoundHttpException();
        }

        /** @var BlogPostData $data */
        $data = $request->toArray();
        $this->mapDataToEntity($data, $blogPost);
        $this->entityManager->flush();

        $view = View::create($this->getDataForEntity($blogPost));
        $view->setFormat('json');
        return $this->viewHandler->handle($view);
    }

    #[Route(path: '/admin/api/blog-posts', methods: ['POST'], name: 'app.blog_posts.post_blog_post')]
    public function postAction(Request $request): Response
    {
        $blogPost = new BlogPost();
        /** @var BlogPostData $data */
        $data = $request->toArray();
        $this->mapDataToEntity($data, $blogPost);
        $this->entityManager->persist($blogPost);
        $this->entityManager->flush();

        $view = View::create($this->getDataForEntity($blogPost));
        $view->setFormat('json');
        $view->setStatusCode(201);
        return $this->viewHandler->handle($view);
    }

    #[Route(path: '/admin/api/blog-posts/{id}', methods: ['DELETE'], name: 'app.blog_posts.delete_blog_post')]
    public function deleteAction(int $id): Response
    {
        /** @var BlogPost $blogPost */
        $blogPost = $this->entityManager->getReference(BlogPost::class, $id);
        $this->entityManager->remove($blogPost);
        $this->entityManager->flush();

        $view = View::create(null);
        $view->setFormat('json');
        $view->setStatusCode(204);
        return $this->viewHandler->handle($view);
    }

    #[Route(path: '/admin/api/blog-posts', methods: ['GET'], name: 'app.blog_posts.cget_blog_post')]
    public function cgetAction(Request $request): Response
    {
        $fieldDescriptors = $this->fieldDescriptorFactory->getFieldDescriptors(BlogPost::RESOURCE_KEY);
        $listBuilder = $this->listBuilderFactory->create(BlogPost::class);
        $this->restHelper->initializeListBuilder($listBuilder, $fieldDescriptors);

        $listRepresentation = new PaginatedRepresentation(
            $listBuilder->execute(),
            BlogPost::RESOURCE_KEY,
            (int) $listBuilder->getCurrentPage(),
            (int) $listBuilder->getLimit(),
            (int) $listBuilder->count()
        );

        $view = View::create($listRepresentation);
        $view->setFormat('json');
        return $this->viewHandler->handle($view);
    }

    /**
     * @return BlogPostData
     */
    protected function getDataForEntity(BlogPost $entity): array
    {
        $image = $entity->getImage();
        $publishedAt = $entity->getPublishedAt();

        return [
            'id' => $entity->getId(),
            'title' => $entity->getTitle(),
            'slug' => $entity->getSlug(),
            'teaser' => $entity->getTeaser(),
            'content' => $entity->getContent(),
            'image' => $image instanceof MediaInterface
                ? ['id' => $image->getId()]
                : null,
            'publishedAt' => $publishedAt ? $publishedAt->format('Y-m-d') : null,
        ];
    }

    /**
     * @param BlogPostData $data
     */
    protected function mapDataToEntity(array $data, BlogPost $entity): void
    {
        $imageId = isset($data['image']) && isset($data['image']['id']) ? $data['image']['id'] : null;
        $publishedAt = isset($data['publishedAt']) && $data['publishedAt'] ? new \DateTime($data['publishedAt']) : null;

        $entity->setTitle($data['title']);
        $entity->setSlug($data['slug']);
        $entity->setTeaser($data['teaser'] ?? null);
        $entity->setContent($data['content'] ?? null);
        $entity->setImage($imageId ? $this->mediaManager->getEntityById($imageId) : null);
        $entity->setPublishedAt($publishedAt);
    }

    public function getSecurityContext(): string
    {
        return BlogPost::SECURITY_CONTEXT;
    }

    public function getLocale(Request $request): ?string
    {
        return $request->query->get('locale');
    }
}

