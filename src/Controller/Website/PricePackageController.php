<?php

namespace App\Controller\Website;

use App\Entity\PricePackage;
use App\Repository\PricePackageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PricePackageController extends AbstractController
{
    /**
     * @Route("/price-packages", name="app_price_packages")
     */
    public function index(PricePackageRepository $repository): Response
    {
        $pricePackages = $repository->findAllOrderedByPrice();
        
        return $this->render('price_packages/index.html.twig', [
            'pricePackages' => $pricePackages
        ]);
    }
    
    /**
     * @Route("/price-packages/{id}", name="app_price_package_detail")
     */
    public function detail(PricePackage $pricePackage): Response
    {
        return $this->render('price_packages/detail.html.twig', [
            'pricePackage' => $pricePackage
        ]);
    }
}