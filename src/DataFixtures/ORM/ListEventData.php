<?php

namespace BestWishes\DataFixtures\ORM;

use BestWishes\Entity\ListEvent;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ListEventData extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $listEvt1 = new ListEvent();
        $listEvt1->setName('Christmas');
        $listEvt1->setDay(25);
        $listEvt1->setMonth(12);
        $listEvt2 = new ListEvent(true);
        $listEvt2->setName('New year\'s eve');
        $listEvt2->setDay(1);
        $listEvt2->setMonth(1);

        $manager->persist($listEvt1);
        $manager->persist($listEvt2);
        $manager->flush();
    }
}
