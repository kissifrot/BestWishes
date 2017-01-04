<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\GiftList;
use AppBundle\Security\Acl\Permissions\BestWishesMaskBuilder;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;

class GiftListData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
        $standardUserList1 = new GiftList();
        $stdUser1 = $this->getReference('standard-user1');
        $standardUserList1->setUser($stdUser1);
        $standardUserList1->setName($stdUser1->getName());
        $standardUserList1->setBirthDate(\DateTime::createFromFormat('Y-m-d', '2010-01-01'));


        $standardUserList2 = new GiftList();
        $stdUser2 = $this->getReference('standard-user2');
        $standardUserList2->setUser($stdUser2);
        $standardUserList2->setName($stdUser2->getName());
        $standardUserList2->setBirthDate(\DateTime::createFromFormat('Y-m-d', '2010-04-01'));

        $manager->persist($standardUserList1);
        $manager->persist($standardUserList2);
        $manager->flush();

        // Add ACLs
        $stdUser3 = $this->getReference('standard-user3');
        $aclProvider = $this->container->get('security.acl.provider');
        $objectIdentity = ObjectIdentity::fromDomainObject($standardUserList1);
        $acl = $aclProvider->createAcl($objectIdentity);
        $securityIdentity1 = UserSecurityIdentity::fromAccount($stdUser1);
        $acl->insertObjectAce($securityIdentity1, BestWishesMaskBuilder::MASK_OWNER);
        $securityIdentity2 = UserSecurityIdentity::fromAccount($stdUser3);
        $acl->insertObjectAce($securityIdentity2, BestWishesMaskBuilder::MASK_SURPRISE_ADD);
        $securityIdentity2 = UserSecurityIdentity::fromAccount($stdUser3);
        $acl->insertObjectAce($securityIdentity2, BestWishesMaskBuilder::MASK_ALERT_ADD);
        $aclProvider->updateAcl($acl);

        $aclProvider = $this->container->get('security.acl.provider');
        $objectIdentity = ObjectIdentity::fromDomainObject($standardUserList2);
        $acl = $aclProvider->createAcl($objectIdentity);
        $securityIdentity1 = UserSecurityIdentity::fromAccount($stdUser1);
        $acl->insertObjectAce($securityIdentity1, BestWishesMaskBuilder::MASK_ALERT_ADD);
        $securityIdentity1 = UserSecurityIdentity::fromAccount($stdUser2);
        $acl->insertObjectAce($securityIdentity1, BestWishesMaskBuilder::MASK_OWNER);
        $securityIdentity2 = UserSecurityIdentity::fromAccount($stdUser3);
        $acl->insertObjectAce($securityIdentity2, BestWishesMaskBuilder::MASK_SURPRISE_ADD);
        $aclProvider->updateAcl($acl);

        $this->addReference('standard-user-list1', $standardUserList1);
        $this->addReference('standard-user-list2', $standardUserList2);
    }

    public function getOrder()
    {
        return 2;
    }
}
