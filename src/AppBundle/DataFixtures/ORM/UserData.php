<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserData extends Fixture
{
    public const ADMIN_USER_REFERENCE = 'admin-user';
    public const USER1_USER_REFERENCE = 'standard-user1';
    public const USER2_USER_REFERENCE = 'standard-user2';
    public const USER3_USER_REFERENCE = 'standard-user3';

    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager): void
    {
        $adminUser = new User();
        $adminUser->setUsername('admin');
        $adminUser->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $adminUser->setName('Admin user');
        $adminUser->setEmail('admin-user@a-mailserever.com');
        $adminUser->setEnabled(true);
        $encoder = $this->container->get('security.password_encoder');
        $password = $encoder->encodePassword($adminUser, 'admin');
        $adminUser->setPassword($password);

        $standardUser1 = new User();
        $standardUser1->setUsername('user1');
        $standardUser1->setName('Standard user 1');
        $standardUser1->setEmail('std-user1@a-mailserever.com');
        $standardUser1->setEnabled(true);
        $encoder = $this->container->get('security.password_encoder');
        $password = $encoder->encodePassword($standardUser1, 'user1');
        $standardUser1->setPassword($password);

        $standardUser2 = new User();
        $standardUser2->setUsername('user2');
        $standardUser2->setName('Standard user 2');
        $standardUser2->setEmail('std-user2@a-mailserever.com');
        $standardUser2->setEnabled(true);
        $encoder = $this->container->get('security.password_encoder');
        $password = $encoder->encodePassword($standardUser2, 'user2');
        $standardUser2->setPassword($password);

        // User without a list
        $standardUser3 = new User();
        $standardUser3->setUsername('user3');
        $standardUser3->setName('Standard user 3');
        $standardUser3->setEmail('std-user3@a-mailserever.com');
        $standardUser3->setEnabled(true);
        $encoder = $this->container->get('security.password_encoder');
        $password = $encoder->encodePassword($standardUser3, 'user3');
        $standardUser3->setPassword($password);

        $manager->persist($adminUser);
        $manager->persist($standardUser1);
        $manager->persist($standardUser2);
        $manager->persist($standardUser3);
        $manager->flush();

        $this->addReference(self::ADMIN_USER_REFERENCE, $adminUser);
        $this->addReference(self::USER1_USER_REFERENCE, $standardUser1);
        $this->addReference(self::USER2_USER_REFERENCE, $standardUser2);
        $this->addReference(self::USER3_USER_REFERENCE, $standardUser3);
    }
}
