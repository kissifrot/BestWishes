<?php

namespace BestWishes\Manager;

use BestWishes\Entity\User;
use BestWishes\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class UserManager
{
    private UserPasswordHasherInterface $passwordHasher;
    private UserRepository $userRepository;

    public function __construct(UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository)
    {
        $this->passwordHasher = $passwordHasher;
        $this->userRepository = $userRepository;
    }

    public function createUser(): User
    {
        return new User();
    }

    public function updatePassword(PasswordAuthenticatedUserInterface $user, string $plainPassword): void
    {
        $encodedPassword = $this->passwordHasher->hashPassword(
            $user,
            $plainPassword
        );

        $user->setPassword($encodedPassword);
    }

    /**
     * @return User[]
     */
    public function findUsers(): array
    {
        return $this->userRepository->findAll();
    }
}
