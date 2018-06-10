<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryData extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // List 1
        $category1 = new Category();
        $category1->setName('Category 1-1');
        $category1->setList($this->getReference(GiftListData::USER1_LIST_REFERENCE));
        $category2 = new Category();
        $category2->setName('Category 2-1');
        $category2->setList($this->getReference(GiftListData::USER1_LIST_REFERENCE));
        $category3 = new Category();
        $category3->setName('Category 3-1');
        $category3->setList($this->getReference(GiftListData::USER1_LIST_REFERENCE));
        $category4 = new Category();
        $category4->setName('Category 4-1');
        $category4->setList($this->getReference(GiftListData::USER1_LIST_REFERENCE));

        $manager->persist($category1);
        $manager->persist($category2);
        $manager->persist($category3);
        $manager->persist($category4);

        $manager->flush();

        $this->addReference('category1-1', $category1);
        $this->addReference('category2-1', $category2);
        $this->addReference('category3-1', $category3);
        $this->addReference('category4-1', $category4);

        // List 2
        $category12 = new Category();
        $category12->setName('Category 1-2');
        $category12->setList($this->getReference(GiftListData::USER2_LIST_REFERENCE));
        $category22 = new Category();
        $category22->setName('Category 2-2');
        $category22->setList($this->getReference(GiftListData::USER2_LIST_REFERENCE));
        $category32 = new Category();
        $category32->setName('Category 3-2');
        $category32->setList($this->getReference(GiftListData::USER2_LIST_REFERENCE));
        $category42 = new Category();
        $category42->setName('Category 4-2');
        $category42->setList($this->getReference(GiftListData::USER2_LIST_REFERENCE));

        $manager->persist($category12);
        $manager->persist($category22);
        $manager->persist($category32);
        $manager->persist($category42);

        $manager->flush();

        $this->addReference('category1-2', $category12);
        $this->addReference('category2-2', $category22);
        $this->addReference('category3-2', $category32);
        $this->addReference('category4-2', $category42);
    }

    /**
     * @inheritdoc
     */
    public function getDependencies(): array
    {
        return [
            UserData::class,
            GiftListData::class
        ];
    }
}
