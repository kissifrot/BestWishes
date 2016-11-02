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
        $adminUser->setEmail('kissifrot@gmail.com');
        $encoder = $this->container->get('security.password_encoder');
        $password = $encoder->encodePassword($adminUser, 'admin');
        $adminUser->setPassword($password);

        $standardUser = new User();
        $standardUser->setUsername('user1');
        $standardUser->setName('Standard user');
        $standardUser->setEmail('kissifrot@gmail.com');
        $encoder = $this->container->get('security.password_encoder');
        $password = $encoder->encodePassword($standardUser, 'user1');
        $standardUser->setPassword($password);

        $manager->persist($adminUser);
        $manager->persist($standardUser);
        $manager->flush();

        $this->addReference('admin-user', $adminUser);
        $this->addReference('standard-user', $standardUser);
    }

    public function getOrder()
    {
        return 1;
    }
}