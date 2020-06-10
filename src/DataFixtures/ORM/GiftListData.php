<?php

namespace BestWishes\DataFixtures\ORM;

use BestWishes\Entity\GiftList;
use BestWishes\Security\Acl\Permissions\BestWishesMaskBuilder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Model\AclProviderInterface;

class GiftListData extends Fixture implements DependentFixtureInterface
{
    public const USER1_LIST_REFERENCE = 'standard-user-list1';
    public const USER2_LIST_REFERENCE = 'standard-user-list2';

    private $aclProvider;

    public function __construct(AclProviderInterface $aclProvider)
    {
        $this->aclProvider = $aclProvider;
    }

    public function load(ObjectManager $manager): void
    {
        $standardUserList1 = new GiftList();
        $stdUser1 = $this->getReference(UserData::USER1_USER_REFERENCE);
        $standardUserList1->setOwner($stdUser1);
        $standardUserList1->setName($stdUser1->getName());
        $standardUserList1->setBirthDate(\DateTimeImmutable::createFromFormat('Y-m-d', '2010-01-01'));


        $standardUserList2 = new GiftList();
        $stdUser2 = $this->getReference(UserData::USER2_USER_REFERENCE);
        $standardUserList2->setOwner($stdUser2);
        $standardUserList2->setName($stdUser2->getName());
        $standardUserList2->setBirthDate(\DateTimeImmutable::createFromFormat('Y-m-d', '2010-04-01'));

        $manager->persist($standardUserList1);
        $manager->persist($standardUserList2);
        $manager->flush();

        // Add ACLs
        $maskBuilder = new BestWishesMaskBuilder();
        $maskBuilder
            ->add(BestWishesMaskBuilder::MASK_SURPRISE_ADD)
            ->add(BestWishesMaskBuilder::MASK_ALERT_ADD);
        $maskSurpriseAndAlert = $maskBuilder->get();

        $stdUser3 = $this->getReference(UserData::USER3_USER_REFERENCE);
        $objectIdentity = ObjectIdentity::fromDomainObject($standardUserList1);
        $acl = $this->aclProvider->createAcl($objectIdentity);
        $securityIdentity1 = UserSecurityIdentity::fromAccount($stdUser1);
        $acl->insertObjectAce($securityIdentity1, BestWishesMaskBuilder::MASK_OWNER);
        $securityIdentity2 = UserSecurityIdentity::fromAccount($stdUser3);
        $acl->insertObjectAce($securityIdentity2, $maskSurpriseAndAlert);
        $this->aclProvider->updateAcl($acl);

        $objectIdentity = ObjectIdentity::fromDomainObject($standardUserList2);
        $acl = $this->aclProvider->createAcl($objectIdentity);
        $securityIdentity1 = UserSecurityIdentity::fromAccount($stdUser1);
        $acl->insertObjectAce($securityIdentity1, $maskSurpriseAndAlert);
        $securityIdentity1 = UserSecurityIdentity::fromAccount($stdUser2);
        $acl->insertObjectAce($securityIdentity1, BestWishesMaskBuilder::MASK_OWNER);
        $securityIdentity2 = UserSecurityIdentity::fromAccount($stdUser3);
        $acl->insertObjectAce($securityIdentity2, $maskSurpriseAndAlert);
        $this->aclProvider->updateAcl($acl);

        $this->addReference(self::USER1_LIST_REFERENCE, $standardUserList1);
        $this->addReference(self::USER2_LIST_REFERENCE, $standardUserList2);
    }

    /**
     * @inheritdoc
     */
    public function getDependencies(): array
    {
        return [
            UserData::class
        ];
    }
}
