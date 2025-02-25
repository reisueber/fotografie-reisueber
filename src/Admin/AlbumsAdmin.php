<?php

namespace App\Admin;

use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactory;

class AlbumsAdmin extends Admin
{
    private $viewBuilderFactory;
    
    public function __construct(ViewBuilderFactory $viewBuilderFactory)
    {
        $this->viewBuilderFactory = $viewBuilderFactory;
    }
    
    public function configureViews(ViewCollection $viewCollection): void
    {
        $viewCollection->add(
            $this->viewBuilderFactory->createResourceTabViewBuilder('albums', '/albums')
                ->setResourceKey('albums')
                ->setTitleProperty('albums.title')
        );
        
        $viewCollection->add(
            $this->viewBuilderFactory->createFormViewBuilder('albums_detail', '/albums/:id')
                ->setResourceKey('albums')
                ->setFormKey('album_details')
                ->setTabTitle('sulu_admin.details')
                ->addToolbarActions([/* ... */])
        );
        
        $viewCollection->add(
            $this->viewBuilderFactory->createListViewBuilder('albums_list', '/albums')
                ->setResourceKey('albums')
                ->setListKey('albums')
                ->setTitle('Albums')
                ->addListAdapters(['table'])
                ->enableSearching()
                ->addToolbarActions(['add'])
        );
        
        $viewCollection->add(
            $this->viewBuilderFactory->createFormViewBuilder('albums_add', '/albums/add')
                ->setResourceKey('albums')
                ->setFormKey('album_details')
                ->setTabTitle('Neues Album')
        );
    }
} 