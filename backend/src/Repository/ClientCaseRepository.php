<?php

namespace App\Repository;

use App\Entity\ClientCase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ClientCaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClientCase::class);
    }

    public function countGroupedByStatus(): array
    {
        return $this->createQueryBuilder('c')
            ->select('c.status, COUNT(c.id) as count')
            ->groupBy('c.status')
            ->getQuery()
            ->getResult();
    }
}
