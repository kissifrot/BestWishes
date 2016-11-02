<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Category;
use AppBundle\Entity\Gift;
use AppBundle\Entity\GiftList;
use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GiftData extends AbstractFixture implements OrderedFixtureInterface
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
                'Gift 5 - cat 2',
                'category2'
            ],
            [
                'Gift 6 - cat 2',
                'category2'
            ],
            [
                'Gift 7 - cat 3',
                'category3'
            ]
        ];
        foreach($data as $giftData) {
            $gift = new Gift();
            $gift->setName($giftData[0]);
            /** @var Category $cat */
            $cat = $this->getReference($giftData[1]);
            $gift->setCategory($cat);
            $manager->persist($gift);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 4;
    }
}