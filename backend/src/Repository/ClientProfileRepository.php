<?php

namespace App\Repository;

use App\Entity\ClientProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ClientProfile>
 */
class ClientProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClientProfile::class);
    }

    /**
     * Returns basic info of all client profiles
     *
     * @return array
     */
    public function findAllClientProfiles(): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.fullName', 'p.phoneNumber', 'p.address', 'p.profilePicture')
            ->getQuery()
            ->getArrayResult();
    }
    public function findAllWithUserEmail(): array
    {
        return $this->createQueryBuilder('cp')
            ->leftJoin('cp.user', 'u')
            ->addSelect('u.email')
            ->getQuery()
            ->getArrayResult(); // 👈 Use array result for cleaner JSON
    }
}
