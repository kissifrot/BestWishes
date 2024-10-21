<?php

namespace BestWishes\Security;

use BestWishes\Entity\User;
use BestWishes\Repository\UserRepository;

class UserProvider
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function findUserByUsernameOrEmail(string $usernameOrEmail): ?User
    {
        if (preg_match('/^.+\@\S+\.\S+$/', $usernameOrEmail)) {
            $user = $this->userRepository->findOneBy(['email' => $usernameOrEmail]);
            if (null !== $user) {
                return $user;
            }
        }

        return $this->userRepository->findOneBy(['username' => $usernameOrEmail]);
    }
}
