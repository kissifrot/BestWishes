<?php

namespace BestWishes\Manager;

use BestWishes\Entity\User;
use BestWishes\Repository\UserRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserManager
{
    private $passwordEncoder;
    private $userRepository;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepository)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
    }

    public function createUser(): User
    {
        return new User();
    }

    public function updatePassword(UserInterface $user, string $plainPassword): void
    {
        $encodedPassword = $this->passwordEncoder->encodePassword(
            $user,
            $plainPassword
        );

        $user->setPassword($encodedPassword);
    }

    /**
     * @return User[]|array
     */
    public function findUsers(): array
    {
        return $this->userRepository->findAll();
    }
}
