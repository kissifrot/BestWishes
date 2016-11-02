<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\GiftList;
use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GiftListData extends AbstractFixture implements OrderedFixtureInterface
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
        $standardUserList = new GiftList();
        /** @var User $stdUser */
        $stdUser = $this->getReference('standard-user');
        $standardUserList->setUser($stdUser);
        $standardUserList->setName($stdUser->getName());
        $standardUserList->setBirthDate(\DateTime::createFromFormat('Y-m-d', '2010-01-01'));

        $manager->persist($standardUserList);
        $manager->flush();

        $this->addReference('standard-user-list', $standardUserList);
    }

    public function getOrder()
    {
        return 2;
    }
}