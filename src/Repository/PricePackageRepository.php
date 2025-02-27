<?php

namespace App\Repository;

use App\Entity\PricePackage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PricePackage|null find($id, $lockMode = null, $lockVersion = null)
 * @method PricePackage|null findOneBy(array $criteria, array $orderBy = null)
 * @method PricePackage[]    findAll()
 * @method PricePackage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PricePackageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PricePackage::class);
    }
    
    /**
     * @return PricePackage[]
     */
    public function findAllOrderedByPrice(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.price', 'ASC')
            ->getQuery()
            ->getResult();
    }
    
    /**
     * @return PricePackage[]
     */
    public function findPublished(): array
    {
        // Hier könnte man später eine Logik für die Veröffentlichung hinzufügen
        return $this->findAll();
    }
}