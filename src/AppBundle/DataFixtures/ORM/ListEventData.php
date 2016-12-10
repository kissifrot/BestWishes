<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Category;
use AppBundle\Entity\ListEvent;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ListEventData extends AbstractFixture implements OrderedFixtureInterface
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
        $listEvt1 = new ListEvent();
        $listEvt1
            ->setName('Christmas')
            ->setDay(25)
            ->setMonth(12)
            ->setPermanent(true);
        $listEvt2 = new ListEvent();
        $listEvt2
            ->setName('New year\'s eve')
            ->setDay(1)
            ->setMonth(1)
            ->setPermanent(true);

        $manager->persist($listEvt1);
        $manager->persist($listEvt2);
        $manager->flush();
    }

    public function getOrder()
    {
        return 5;
    }
}
