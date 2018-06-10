<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\ListEvent;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ListEventData extends Fixture
{
    public function load(ObjectManager $manager): void
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
}
