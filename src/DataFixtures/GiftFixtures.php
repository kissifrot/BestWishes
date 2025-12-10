<?php

namespace BestWishes\DataFixtures;

use BestWishes\Entity\Category;
use BestWishes\Entity\Gift;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class GiftFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $maxGiftsForList1 = random_int(10, 20);
        $maxGiftsForList2 = random_int(5, 15);
        for ($i = 1; $i <= $maxGiftsForList1; $i++) {
            $categoryNumber = random_int(1, 5);
            $gift = new Gift(false, $this->getReference(\sprintf('category-1-%u', $categoryNumber), Category::class));
            $gift->setName(\sprintf('Gift %u Category %u', $i, $categoryNumber));
            $manager->persist($gift);
        }
        for ($i = 1; $i <= $maxGiftsForList2; $i++) {
            $categoryNumber = random_int(1, 5);
            $gift = new Gift(false, $this->getReference(\sprintf('category-2-%u', $categoryNumber), Category::class));
            $gift->setName(\sprintf('Gift %u Category %u', $i, $categoryNumber));
            $manager->persist($gift);
        }
        $manager->flush();


    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }
}
