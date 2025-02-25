<?php

// src/Admin/AlbumAdmin.php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Album;
use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItemCollection;
use Sulu\Bundle\AdminBundle\Admin\View\ToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Component\Security\Authorization\PermissionTypes;
use Sulu\Component\Security\Authorization\SecurityCheckerInterface;

class AlbumAdmin extends Admin
{
    final public const SECURITY_SYSTEM = 'Sulu';
    final public const LIST_VIEW = 'app.albums.list';
    final public const ADD_FORM_VIEW = 'app.albums.add_form';
    final public const ADD_FORM_DETAILS_VIEW = 'app.albums.add_form.details';
    final public const EDIT_FORM_VIEW = 'app.albums.edit_form';
    final public const EDIT_FORM_DETAILS_VIEW = 'app.albums.edit_form.details';

    public function __construct(
        private ViewBuilderFactoryInterface $viewBuilderFactory,
        private SecurityCheckerInterface $securityChecker
    ) {
    }

    public function configureNavigationItems(NavigationItemCollection $navigationItemCollection): void
    {
        //if ($this->securityChecker->hasPermission(Album::SECURITY_CONTEXT, PermissionTypes::EDIT)) {
            $albums = new NavigationItem('app.albums');
            $albums->setPosition(30);
            $albums->setView(static::LIST_VIEW);
            $albums->setIcon('su-image');
            
            $navigationItemCollection->add($albums);
        //}
    }

    public function configureViews(ViewCollection $viewCollection): void
    {
        $listView = $this->viewBuilderFactory->createListViewBuilder(static::LIST_VIEW, '/albums')
            ->setResourceKey(Album::RESOURCE_KEY)
            ->setListKey(Album::LIST_KEY)
            ->addListAdapters(['table'])
            ->addToolbarActions([
                new ToolbarAction('sulu_admin.add'),
                new ToolbarAction('sulu_admin.delete'),
            ])
            ->setAddView(static::ADD_FORM_VIEW)
            ->setEditView(static::EDIT_FORM_VIEW);
        $viewCollection->add($listView);

        $addFormView = $this->viewBuilderFactory->createResourceTabViewBuilder(static::ADD_FORM_VIEW, '/albums/add')
            ->setResourceKey(Album::RESOURCE_KEY)
            ->setBackView(static::LIST_VIEW);
        $viewCollection->add($addFormView);

        $addDetailsFormView = $this->viewBuilderFactory->createFormViewBuilder(static::ADD_FORM_DETAILS_VIEW, '/details')
            ->setResourceKey(Album::RESOURCE_KEY)
            ->setFormKey(Album::FORM_KEY)
            ->setTabTitle('sulu_admin.details')
            ->setEditView(static::EDIT_FORM_VIEW)
            ->addToolbarActions([new ToolbarAction('sulu_admin.save')])
            ->setParent(static::ADD_FORM_VIEW);
        $viewCollection->add($addDetailsFormView);

        $editFormView = $this->viewBuilderFactory->createResourceTabViewBuilder(static::EDIT_FORM_VIEW, '/albums/:id')
            ->setResourceKey(Album::RESOURCE_KEY)
            ->setBackView(static::LIST_VIEW);
        $viewCollection->add($editFormView);

        $editDetailsFormView = $this->viewBuilderFactory->createFormViewBuilder(static::EDIT_FORM_DETAILS_VIEW, '/details')
            ->setResourceKey(Album::RESOURCE_KEY)
            ->setFormKey(Album::FORM_KEY)
            ->setTabTitle('sulu_admin.details')
            ->addToolbarActions([
                new ToolbarAction('sulu_admin.save'),
                new ToolbarAction('sulu_admin.delete'),
            ])
            ->setParent(static::EDIT_FORM_VIEW);
        $viewCollection->add($editDetailsFormView);
    }

    public function getSecurityContexts()
    {
        return [
            self::SECURITY_SYSTEM => [
                'Music' => [
                    Album::SECURITY_CONTEXT => [
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