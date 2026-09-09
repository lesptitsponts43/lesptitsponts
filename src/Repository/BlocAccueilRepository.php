<?php

namespace App\Repository;

use App\Entity\BlocAccueil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BlocAccueil>
 */
class BlocAccueilRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlocAccueil::class);
    }
}
