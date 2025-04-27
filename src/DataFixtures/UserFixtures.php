<?php

namespace BestWishes\DataFixtures;

use BestWishes\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const ADMIN_USER_REFERENCE = 'admin-user';
    public const BASE_USER_1_REFERENCE = 'base-user-1';
    public const BASE_USER_2_REFERENCE = 'base-user-2';

    public function __construct(private readonly UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $baseUser1 = new User();
        $baseUser1->setName('User 1');
        $baseUser1->setUserIdentifier('user1');
        $baseUser1->setEmail('user1@example.com');

        $password = $this->hasher->hashPassword($baseUser1, 'user1');
        $baseUser1->setPassword($password);
        $manager->persist($baseUser1);
        $baseUser2 = new User();
        $baseUser2->setName('User 2');
        $baseUser2->setUserIdentifier('user2');
        $baseUser2->setEmail('user2@example.com');
        $password = $this->hasher->hashPassword($baseUser2, 'user2');
        $baseUser2->setPassword($password);
        $manager->persist($baseUser2);

        $adminUser = new User();
        $adminUser->setName('Admin User');
        $adminUser->setUserIdentifier('admin');
        $adminUser->setEmail('admin@example.com');
        $password = $this->hasher->hashPassword($adminUser, 'admin');
        $adminUser->setPassword($password);
        $adminUser->setRoles(['ROLE_ADMIN']);
        $manager->persist($adminUser);

        $manager->flush();

        $this->addReference(self::ADMIN_USER_REFERENCE, $adminUser);
        $this->addReference(self::BASE_USER_1_REFERENCE, $baseUser1);
        $this->addReference(self::BASE_USER_2_REFERENCE, $baseUser2);
    }
}
