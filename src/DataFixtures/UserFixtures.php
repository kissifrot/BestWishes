<?php

namespace BestWishes\DataFixtures;

use BestWishes\Entity\GiftList;
use BestWishes\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public const ADMIN_USER_REFERENCE = 'admin-user';
    public const BASE_USER_1_REFERENCE = 'base-user-1';
    public const BASE_USER_2_REFERENCE = 'base-user-2';

    public function load(ObjectManager $manager): void
    {
        $baseUser1 = new User();
        $baseUser1->setName('User 1');
        $baseUser1->setUserIdentifier('user1');
        $manager->persist($baseUser1);
        $baseUser2 = new User();
        $baseUser2->setName('User 2');
        $baseUser2->setUserIdentifier('user2');
        $manager->persist($baseUser2);

        $adminUser = new User();
        $adminUser->setName('Admin User');
        $adminUser->setUserIdentifier('admin');
        $manager->persist($adminUser);

        $manager->flush();

        $this->addReference(self::ADMIN_USER_REFERENCE, $adminUser);
        $this->addReference(self::BASE_USER_1_REFERENCE, $baseUser1);
        $this->addReference(self::BASE_USER_2_REFERENCE, $baseUser2);
    }
}