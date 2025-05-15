<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Doctrine\ORM\EntityManagerInterface;

class UserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $identifier]);

        if (!$user) {
            throw new UserNotFoundException(sprintf('User with email "%s" not found.', $identifier));
        }

        return $user;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        return $user;
    }

    public function supportsClass(string $class): bool
    {
        return $class === User::class;
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
{
    $user->setPassword($newHashedPassword);
    $this->entityManager->flush();
}
}
