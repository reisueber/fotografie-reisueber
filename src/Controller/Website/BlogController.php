<?php

declare(strict_types=1);

namespace App\Controller\Website;

use App\Entity\BlogPost;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Bundle\MediaBundle\Api\Media;
use Sulu\Bundle\MediaBundle\Media\Manager\MediaManagerInterface;
use Sulu\Bundle\WebsiteBundle\Controller\WebsiteController;
use Sulu\Component\Content\Compat\StructureInterface;
use Sulu\Component\Content\Compat\StructureManagerInterface;
use Sulu\Component\DocumentManager\DocumentManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends WebsiteController
{
    private EntityManagerInterface $entityManager;
    private MediaManagerInterface $mediaManager;
    private DocumentManagerInterface $documentManager;
    private StructureManagerInterface $structureManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        MediaManagerInterface $mediaManager,
        DocumentManagerInterface $documentManager,
        StructureManagerInterface $structureManager
    ) {
        $this->entityManager = $entityManager;
        $this->mediaManager = $mediaManager;
        $this->documentManager = $documentManager;
        $this->structureManager = $structureManager;
    }

    public function indexAction(StructureInterface $structure, Request $request): Response
    {
        $postsPerPage = (int) ($structure->getProperty('postsPerPage')->getValue() ?? 12);
        $posts = $this->getBlogPosts($request->getLocale(), $postsPerPage);

        return $this->renderStructure($structure, [
            'posts' => $posts,
        ]);
    }

    public function fallbackIndexAction(Request $request): Response
    {
        $posts = $this->getBlogPosts($request->getLocale(), 12);
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
                    $document = $this->documentManager->find($path, $locale);
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
                        $headerImage = $this->getMediaData($headerImageValue, $locale);
                    }
                }
                if ($structure->hasProperty('header_image_mobile')) {
                    $headerImageMobileValue = $structure->getProperty('header_image_mobile')->getValue();
                    if ($headerImageMobileValue) {
                        $headerImageMobile = $this->getMediaData($headerImageMobileValue, $locale);
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

        return $this->render('pages/blog.html.twig', [
            'posts' => $posts,
            'content' => [
                'title' => $title,
                'subtitle' => $subtitle,
                'header_image' => $headerImage,
                'header_image_mobile' => $headerImageMobile,
            ],
        ]);
    }
    
    private function getMediaData($mediaData, string $locale): ?array
    {
        if (!$mediaData) {
            return null;
        }
        
        try {
            $mediaId = is_array($mediaData) ? ($mediaData['id'] ?? null) : $mediaData;
            if (!$mediaId) {
                return null;
            }
            
            $media = $this->mediaManager->getById($mediaId, $locale);
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

    private function getBlogPosts(string $locale, int $limit = 12): array
    {
        $repository = $this->entityManager->getRepository(BlogPost::class);
        
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
                    $media = $this->mediaManager->getById($blogPost->getImage()->getId(), $locale);
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
    public function showAction(string $slug, Request $request): Response
    {
        $locale = $request->getLocale();
        $repository = $this->entityManager->getRepository(BlogPost::class);
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
                $media = $this->mediaManager->getById($blogPost->getImage()->getId(), $locale);
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

        return $this->render('pages/blog-detail.html.twig', [
            'post' => $post,
        ]);
    }
}

