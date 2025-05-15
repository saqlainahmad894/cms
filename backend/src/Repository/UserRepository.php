<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function countByRole(string $role): int
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT COUNT(*) as total FROM user u 
                JOIN user_roles ur ON u.id = ur.user_id 
                JOIN role r ON ur.role_id = r.id 
                WHERE r.name = :role";

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery(['role' => $role]);

        return (int) $result->fetchOne();
    }
}
