<?php

namespace App\Admin;

use App\Entity\BlogPost;
use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItemCollection;
use Sulu\Bundle\AdminBundle\Admin\View\ToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactory;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Component\Security\Authorization\PermissionTypes;

class BlogPostAdmin extends Admin
{
    private $viewBuilderFactory;

    public function __construct(ViewBuilderFactory $viewBuilderFactory)
    {
        $this->viewBuilderFactory = $viewBuilderFactory;
    }

    public function configureNavigationItems(NavigationItemCollection $navigationItemCollection): void
    {
        $blogModule = new NavigationItem('app.blog_posts');
        $blogModule->setPosition(40);
        $blogModule->setIcon('su-newspaper');
        $blogModule->setView('blog_posts_list');

        $navigationItemCollection->add($blogModule);
    }

    public function configureViews(ViewCollection $viewCollection): void
    {
        $listView = $this->viewBuilderFactory->createListViewBuilder('blog_posts_list', '/blog-posts')
            ->setResourceKey(BlogPost::RESOURCE_KEY)
            ->setListKey(BlogPost::LIST_KEY)
            ->setTitle('app.blog_posts')
            ->addListAdapters(['table'])
            ->enableSearching()
            ->addToolbarActions([
                new ToolbarAction('sulu_admin.add'),
                new ToolbarAction('sulu_admin.delete'),
            ])
            ->setAddView('blog_posts_add')
            ->setEditView('blog_posts_detail');
        $viewCollection->add($listView);

        $addFormView = $this->viewBuilderFactory->createResourceTabViewBuilder('blog_posts_add', '/blog-posts/add')
            ->setResourceKey(BlogPost::RESOURCE_KEY)
            ->setBackView('blog_posts_list');
        $viewCollection->add($addFormView);

        $addDetailsFormView = $this->viewBuilderFactory->createFormViewBuilder('blog_posts_add_details', '/details')
            ->setResourceKey(BlogPost::RESOURCE_KEY)
            ->setFormKey(BlogPost::FORM_KEY)
            ->setTabTitle('sulu_admin.details')
            ->setEditView('blog_posts_detail')
            ->addToolbarActions([new ToolbarAction('sulu_admin.save')])
            ->setParent('blog_posts_add');
        $viewCollection->add($addDetailsFormView);

        $editFormView = $this->viewBuilderFactory->createResourceTabViewBuilder('blog_posts_detail', '/blog-posts/:id')
            ->setResourceKey(BlogPost::RESOURCE_KEY)
            ->setBackView('blog_posts_list');
        $viewCollection->add($editFormView);

        $editDetailsFormView = $this->viewBuilderFactory->createFormViewBuilder('blog_posts_detail_details', '/details')
            ->setResourceKey(BlogPost::RESOURCE_KEY)
            ->setFormKey(BlogPost::FORM_KEY)
            ->setTabTitle('sulu_admin.details')
            ->addToolbarActions([
                new ToolbarAction('sulu_admin.save'),
                new ToolbarAction('sulu_admin.delete'),
            ])
            ->setParent('blog_posts_detail');
        $viewCollection->add($editDetailsFormView);
    }

    public function getSecurityContexts()
    {
        return [
            'Sulu' => [
                'Blog' => [
                    BlogPost::SECURITY_CONTEXT => [
                        PermissionTypes::VIEW,
                        PermissionTypes::ADD,
                        PermissionTypes::EDIT,
                        PermissionTypes::DELETE,
                    ],
                ],
            ],
        ];
    }
}

