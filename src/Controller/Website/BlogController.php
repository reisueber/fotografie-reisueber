<?php

declare(strict_types=1);

namespace App\Controller\Website;

use App\Entity\BlogPost;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Bundle\MediaBundle\Api\Media;
use Sulu\Bundle\MediaBundle\Media\Manager\MediaManagerInterface;
use Sulu\Bundle\WebsiteBundle\Controller\WebsiteController;
use Sulu\Component\Content\Compat\StructureInterface;
use Sulu\Component\DocumentManager\DocumentManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class BlogController extends WebsiteController
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }
    public function indexAction(
        StructureInterface $structure,
        Request $request,
        EntityManagerInterface $entityManager,
        MediaManagerInterface $mediaManager
    ): Response {
        if ('html' !== $request->getRequestFormat()) {
            $request->setRequestFormat('html');
            $request->attributes->set('_format', 'html');
        }

        $postsPerPage = (int) ($structure->getProperty('postsPerPage')->getValue() ?? 12);
        $posts = $this->getBlogPosts($request->getLocale(), $postsPerPage, $entityManager, $mediaManager);

        return $this->renderStructure($structure, [
            'posts' => $posts,
        ]);
    }

    public function fallbackIndexAction(
        Request $request,
        EntityManagerInterface $entityManager,
        MediaManagerInterface $mediaManager,
        DocumentManagerInterface $documentManager
    ): Response {
        error_log('fallbackIndexAction called');
        $posts = $this->getBlogPosts($request->getLocale(), 12, $entityManager, $mediaManager);
        error_log('fallbackIndexAction: posts count = ' . count($posts));
        $locale = $request->getLocale();

        $headerImage = null;
        $headerImageMobile = null;
        $title = 'Blog';
        $subtitle = 'Einblicke in unsere Arbeit und Leidenschaft für Fotografie';

        try {
            $paths = [
                '/cmf/website/contents/' . $locale . '/blog',
                '/cmf/website/contents/blog'
            ];

            $document = null;
            foreach ($paths as $path) {
                try {
                    $document = $documentManager->find($path, $locale);
                    if ($document) {
                        break;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            if ($document) {
                $structure = $document->getStructure();

                if ($structure->hasProperty('header_image')) {
                    $headerImageValue = $structure->getProperty('header_image')->getValue();
                    if ($headerImageValue) {
                        $headerImage = $this->getMediaData($headerImageValue, $locale, $mediaManager);
                    }
                }
                if ($structure->hasProperty('header_image_mobile')) {
                    $headerImageMobileValue = $structure->getProperty('header_image_mobile')->getValue();
                    if ($headerImageMobileValue) {
                        $headerImageMobile = $this->getMediaData($headerImageMobileValue, $locale, $mediaManager);
                    }
                }
                if ($structure->hasProperty('title')) {
                    $title = $structure->getProperty('title')->getValue();
                }
                if ($structure->hasProperty('subtitle')) {
                    $subtitle = $structure->getProperty('subtitle')->getValue();
                }
            }
        } catch (\Exception $e) {
            // If document not found, use defaults
        }

        $content = $this->twig->render('pages/blog.html.twig', [
            'posts' => $posts,
            'content' => [
                'title' => $title,
                'subtitle' => $subtitle,
                'header_image' => $headerImage,
                'header_image_mobile' => $headerImageMobile,
            ],
        ]);

        return new Response($content);
    }

    private function getMediaData($mediaData, string $locale, MediaManagerInterface $mediaManager): ?array
    {
        if (!$mediaData) {
            return null;
        }

        try {
            $mediaId = is_array($mediaData) ? ($mediaData['id'] ?? null) : $mediaData;
            if (!$mediaId) {
                return null;
            }

            $media = $mediaManager->getById($mediaId, $locale);
            if ($media instanceof Media) {
                return [
                    'url' => $media->getUrl(),
                    'title' => $media->getTitle(),
                    'thumbnails' => $media->getThumbnails(),
                ];
            }
        } catch (\Exception $e) {
            // Ignore media errors
        }

        return null;
    }

    private function getBlogPosts(string $locale, int $limit, EntityManagerInterface $entityManager, MediaManagerInterface $mediaManager): array
    {
        $repository = $entityManager->getRepository(BlogPost::class);

        $blogPosts = $repository->createQueryBuilder('bp')
            ->where('bp.publishedAt IS NOT NULL')
            ->andWhere('bp.publishedAt <= :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('bp.publishedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $posts = [];
        foreach ($blogPosts as $blogPost) {
            $imageData = null;
            if ($blogPost->getImage()) {
                try {
                    $media = $mediaManager->getById($blogPost->getImage()->getId(), $locale);
                    if ($media instanceof Media) {
                        $imageData = [
                            'url' => $media->getUrl(),
                            'title' => $media->getTitle(),
                            'thumbnails' => $media->getThumbnails(),
                        ];
                    }
                } catch (\Exception $e) {
                    // Ignore media errors
                }
            }

            $posts[] = [
                'id' => $blogPost->getId(),
                'title' => $blogPost->getTitle(),
                'slug' => $blogPost->getSlug(),
                'teaser' => $blogPost->getTeaser(),
                'publishedAt' => $blogPost->getPublishedAt(),
                'image' => $imageData,
            ];
        }

        return $posts;
    }

    #[Route('/blog/{slug}', name: 'blog_show', priority: 10)]
    public function showAction(
        string $slug,
        Request $request,
        EntityManagerInterface $entityManager,
        MediaManagerInterface $mediaManager
    ): Response {
        $locale = $request->getLocale();
        $repository = $entityManager->getRepository(BlogPost::class);
        $blogPost = $repository->findOneBy(['slug' => $slug]);

        if (!$blogPost instanceof BlogPost) {
            throw new NotFoundHttpException('Blog post not found');
        }

        if ($blogPost->getPublishedAt() === null || $blogPost->getPublishedAt() > new \DateTime()) {
            throw new NotFoundHttpException('Blog post not published yet');
        }

        $imageData = null;
        if ($blogPost->getImage()) {
            try {
                $media = $mediaManager->getById($blogPost->getImage()->getId(), $locale);
                if ($media instanceof Media) {
                    $imageData = [
                        'url' => $media->getUrl(),
                        'title' => $media->getTitle(),
                        'thumbnails' => $media->getThumbnails(),
                    ];
                }
            } catch (\Exception $e) {
                // Ignore media errors
            }
        }

        $post = [
            'id' => $blogPost->getId(),
            'title' => $blogPost->getTitle(),
            'slug' => $blogPost->getSlug(),
            'teaser' => $blogPost->getTeaser(),
            'content' => $blogPost->getContent(),
            'publishedAt' => $blogPost->getPublishedAt(),
            'image' => $imageData,
        ];

        $content = $this->twig->render('pages/blog-detail.html.twig', [
            'post' => $post,
        ]);

        return new Response($content);
    }
}

