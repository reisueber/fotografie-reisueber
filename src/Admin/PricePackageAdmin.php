<?php

namespace App\Admin;

use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItemCollection;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactory;
use App\Entity\PricePackage;
use Sulu\Component\Security\Authorization\PermissionTypes;
use Sulu\Component\Security\Authorization\SecurityCheckerInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ToolbarAction;

class PricePackageAdmin extends Admin
{
    private $viewBuilderFactory;
    private $securityChecker;
    
    public function __construct(
        ViewBuilderFactory $viewBuilderFactory,
        SecurityCheckerInterface $securityChecker = null
    ) {
        $this->viewBuilderFactory = $viewBuilderFactory;
        $this->securityChecker = $securityChecker;
    }
    
    public function configureNavigationItems(NavigationItemCollection $navigationItemCollection): void
    {
        $pricePackages = new NavigationItem('app.price_packages');
        $pricePackages->setPosition(40);
        $pricePackages->setView('price_packages_list');
        $pricePackages->setIcon('su-tag'); // Oder ein passendes Icon
        
        $navigationItemCollection->add($pricePackages);
    }
    
    public function configureViews(ViewCollection $viewCollection): void
    {
        // List view
        $listView = $this->viewBuilderFactory->createListViewBuilder('price_packages_list', '/price-packages')
            ->setResourceKey(PricePackage::RESOURCE_KEY)
            ->setListKey('price_packages')
            ->setTitle('app.price_packages')
            ->addListAdapters(['table'])
            ->enableSearching()
            ->addToolbarActions([
                new ToolbarAction('sulu_admin.add'),
                new ToolbarAction('sulu_admin.delete'),
            ])
            ->setAddView('price_packages_add')
            ->setEditView('price_packages_detail');
        $viewCollection->add($listView);

        // Add form view
        $addFormView = $this->viewBuilderFactory->createResourceTabViewBuilder('price_packages_add', '/price-packages/add')
            ->setResourceKey(PricePackage::RESOURCE_KEY)
            ->setBackView('price_packages_list');
        $viewCollection->add($addFormView);

        // Add details form view
        $addDetailsFormView = $this->viewBuilderFactory->createFormViewBuilder('price_packages_add_details', '/details')
            ->setResourceKey(PricePackage::RESOURCE_KEY)
            ->setFormKey(PricePackage::FORM_KEY)
            ->setTabTitle('sulu_admin.details')
            ->setEditView('price_packages_detail')
            ->addToolbarActions([new ToolbarAction('sulu_admin.save')])
            ->setParent('price_packages_add');
        $viewCollection->add($addDetailsFormView);

        // Edit form view
        $editFormView = $this->viewBuilderFactory->createResourceTabViewBuilder('price_packages_detail', '/price-packages/:id')
            ->setResourceKey(PricePackage::RESOURCE_KEY)
            ->setBackView('price_packages_list');
        $viewCollection->add($editFormView);

        // Edit details form view
        $editDetailsFormView = $this->viewBuilderFactory->createFormViewBuilder('price_packages_detail_details', '/details')
            ->setResourceKey(PricePackage::RESOURCE_KEY)
            ->setFormKey(PricePackage::FORM_KEY)
            ->setTabTitle('sulu_admin.details')
            ->addToolbarActions([
                new ToolbarAction('sulu_admin.save'),
                new ToolbarAction('sulu_admin.delete'),
            ])
            ->setParent('price_packages_detail');
        $viewCollection->add($editDetailsFormView);
    }

    public function getSecurityContexts()
    {
        return [
            'Sulu' => [
                'Settings' => [
                    PricePackage::SECURITY_CONTEXT => [
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