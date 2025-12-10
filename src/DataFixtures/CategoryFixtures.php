<?php

namespace BestWishes\DataFixtures;

use BestWishes\Entity\Category;
use BestWishes\Entity\GiftList;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $category = new Category();
            $category->setName(\sprintf('Category %u', $i));
            $category->setList($this->getReference(GiftListFixtures::GIFT_LIST_1_REFERENCE, GiftList::class));
            $manager->persist($category);

            $this->addReference(\sprintf('category-1-%u', $i), $category);
        }
        for ($i = 1; $i <= 5; $i++) {
            $category = new Category();
            $category->setName(\sprintf('Category %u', $i));
            $category->setList($this->getReference(GiftListFixtures::GIFT_LIST_2_REFERENCE, GiftList::class));
            $manager->persist($category);

            $this->addReference(\sprintf('category-2-%u', $i), $category);
        }
        $manager->flush();


    }

    public function getDependencies(): array
    {
        return [
            GiftListFixtures::class,
        ];
    }
}
