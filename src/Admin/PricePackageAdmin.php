<?php

namespace App\Admin;

use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactory;

class PricePackageAdmin extends Admin
{
    private $viewBuilderFactory;
    
    public function __construct(ViewBuilderFactory $viewBuilderFactory)
    {
        $this->viewBuilderFactory = $viewBuilderFactory;
    }
    
    public function configureViews(ViewCollection $viewCollection): void
    {
        $viewCollection->add(
            $this->viewBuilderFactory->createResourceTabViewBuilder('price_packages', '/price_packages')
                ->setResourceKey('price_packages')
                ->setTitleProperty('price_packages.title')
        );
        
        $viewCollection->add(
            $this->viewBuilderFactory->createFormViewBuilder('price_packages_detail', '/price_packages/:id')
                ->setResourceKey('price_packages')
                ->setFormKey('price_package_details')
                ->setTabTitle('sulu_admin.details')
                ->addToolbarActions([/* ... */])
        );
        
        $viewCollection->add(
            $this->viewBuilderFactory->createListViewBuilder('price_packages_list', '/price_packages')
                ->setResourceKey('price_packages')
                ->setListKey('price_packages')
                ->setTitle('Price Packages')
                ->addListAdapters(['table'])
                ->enableSearching()
                ->addToolbarActions(['add'])
        );
        
        $viewCollection->add(
            $this->viewBuilderFactory->createFormViewBuilder('price_packages_add', '/price_packages/add')
                ->setResourceKey('price_packages')
                ->setFormKey('price_package_details')
                ->setTabTitle('Neues Preis Paket')
        );
    }
} 