<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Category;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CategoryData extends AbstractFixture implements OrderedFixtureInterface
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
        
        $category1 = new Category();
        $category1->setName('Category 1');
        $category1->setList($this->getReference('standard-user-list'));
        $category2 = new Category();
        $category2->setName('Category 2');
        $category2->setList($this->getReference('standard-user-list'));
        $category3 = new Category();
        $category3->setName('Category 3');
        $category3->setList($this->getReference('standard-user-list'));
        $category4 = new Category();
        $category4->setName('Category 4');
        $category4->setList($this->getReference('standard-user-list'));

        $manager->persist($category1);
        $manager->persist($category2);
        $manager->persist($category3);
        $manager->persist($category4);
        $manager->flush();

        $this->addReference('category1', $category1);
        $this->addReference('category2', $category2);
        $this->addReference('category3', $category3);
        $this->addReference('category4', $category4);
    }

    public function getOrder()
    {
        return 3;
    }
}