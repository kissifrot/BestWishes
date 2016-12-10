<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
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

        $this->addReference('admin-user', $adminUser);
        $this->addReference('standard-user1', $standardUser1);
        $this->addReference('standard-user2', $standardUser2);
        $this->addReference('standard-user3', $standardUser3);
    }

    public function getOrder()
    {
        return 1;
    }
}
