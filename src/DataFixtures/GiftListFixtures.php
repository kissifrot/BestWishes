<?php

namespace BestWishes\DataFixtures;

use BestWishes\Entity\GiftList;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class GiftListFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $giftList1 = new GiftList();
        $giftList1->setName('Gift List 1');
        $giftList1->setOwner($this->getReference(UserFixtures::BASE_USER_1_REFERENCE));

        $manager->persist($giftList1);

        $giftList2 = new GiftList();
        $giftList2->setName('Gift List 2');
        $giftList2->setOwner($this->getReference(UserFixtures::BASE_USER_2_REFERENCE));

        $manager->persist($giftList2);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class
        ];
    }
}