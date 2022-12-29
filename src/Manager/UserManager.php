<?php

namespace BestWishes\Manager;

use BestWishes\Entity\User;
use BestWishes\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class UserManager
{

    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher, private readonly UserRepository $userRepository)
    {
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

        /** @var User $user */
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
