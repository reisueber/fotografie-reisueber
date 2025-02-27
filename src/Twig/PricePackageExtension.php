<?php
// src/Twig/PricePackageExtension.php

namespace App\Twig;

use App\Entity\PricePackage;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PricePackageExtension extends AbstractExtension
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('price_packages', [$this, 'getPricePackages']),
        ];
    }

    public function getPricePackages()
    {
        return $this->entityManager->getRepository(PricePackage::class)->findAll();
    }
}