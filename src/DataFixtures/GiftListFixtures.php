<?php

namespace BestWishes\DataFixtures;

use BestWishes\Entity\GiftList;
use BestWishes\Entity\User;
use BestWishes\Security\PermissionManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class GiftListFixtures extends Fixture implements DependentFixtureInterface
{
    public const GIFT_LIST_1_REFERENCE = 'gift-list-1';
    public const GIFT_LIST_2_REFERENCE = 'gift-list-2';

    public function __construct(private readonly PermissionManager $permissionManager)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $giftList1 = new GiftList();
        $giftList1->setName('Gift List 1');
        $giftList1->setSlug('gift-list-1');
        $giftList1->setBirthDate(new \DateTimeImmutable('1969-12-28'));
        $giftList1->setOwner($this->getReference(UserFixtures::BASE_USER_1_REFERENCE, User::class));

        $manager->persist($giftList1);

        $giftList2 = new GiftList();
        $giftList2->setName('Gift List 2');
        $giftList2->setSlug('gift-list-2');
        $giftList2->setBirthDate(new \DateTimeImmutable('1953-03-16'));
        $giftList2->setOwner($this->getReference(UserFixtures::BASE_USER_2_REFERENCE, User::class));

        $manager->persist($giftList2);
        $manager->flush();
        $this->permissionManager->grant(
            $giftList1,
            $this->getReference(UserFixtures::BASE_USER_1_REFERENCE, User::class),
            'OWNER'
        );
        $this->permissionManager->grant(
            $giftList2,
            $this->getReference(UserFixtures::BASE_USER_2_REFERENCE, User::class),
            'OWNER'
        );

        $this->addReference(self::GIFT_LIST_1_REFERENCE, $giftList1);
        $this->addReference(self::GIFT_LIST_2_REFERENCE, $giftList2);
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class
        ];
    }
}
