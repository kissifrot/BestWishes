<?php

namespace BestWishes\DataFixtures\ORM;

use BestWishes\Entity\Category;
use BestWishes\Entity\Gift;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class GiftData extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $data = [
            [
                'Gift 1 - cat 1',
                'category1'
            ],
            [
                'Gift 2 - cat 1',
                'category1'
            ],
            [
                'Gift 3 - cat 1',
                'category1'
            ],
            [
                'Gift 4 - cat 1',
                'category1'
            ],
            [
                'Gift 5 - cat 1',
                'category1'
            ],
            [
                'Gift 6 - cat 1',
                'category1'
            ],
            [
                'Gift 7 - cat 1',
                'category1'
            ],
            [
                'Gift 8 - cat 1',
                'category1'
            ],
            [
                'Gift 9 - cat 1',
                'category1'
            ],
            [
                'Gift 10 - cat 1',
                'category1'
            ],
            [
                'Gift 11 - cat 1',
                'category1'
            ],
            [
                'Gift 12 - cat 1',
                'category1'
            ],
            [
                'Gift 13 - cat 1',
                'category1'
            ],
            [
                'Gift 14 - cat 1',
                'category1'
            ],
            [
                'Gift 15 - cat 1',
                'category1'
            ],
            [
                'Gift 16 - cat 1',
                'category1'
            ],
            [
                'Gift 17 - cat 1',
                'category1'
            ],
            [
                'Gift 18 - cat 1',
                'category1'
            ],
            [
                'Gift 19 - cat 1',
                'category1'
            ],
            [
                'Gift 20 - cat 1',
                'category1'
            ],
            [
                'Gift 21 - cat 1',
                'category1'
            ],
            [
                'Gift 22 - cat 1',
                'category1'
            ],
            [
                'Gift 23 - cat 1',
                'category1'
            ],
            [
                'Gift 24 - cat 1',
                'category1'
            ],
            [
                'Gift 25 - cat 1',
                'category1'
            ],
            [
                'Gift 26 - cat 2',
                'category2'
            ],
            [
                'Gift 27 - cat 2',
                'category2'
            ],
            [
                'Gift 28 - cat 3',
                'category3'
            ]
        ];
        for ($i = 1; $i <= 2; $i++) {
            foreach ($data as $giftData) {
                $cat = $this->getReference($giftData[1] . '-' . $i);
                $gift = new Gift(false, $cat);
                $gift->setName($giftData[0]);
                /** @var Category $cat */
                $manager->persist($gift);
            }
        }

        $manager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getDependencies(): array
    {
        return [
            GiftListData::class,
            CategoryData::class
        ];
    }
}
