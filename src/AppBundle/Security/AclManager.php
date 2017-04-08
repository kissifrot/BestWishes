<?php

namespace AppBundle\Security;

use AppBundle\Security\Acl\Permissions\BestWishesMaskBuilder;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Model\AuditableEntryInterface;
use Symfony\Component\Security\Acl\Model\MutableAclInterface;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Model\SecurityIdentityInterface;

/**
 * Class to simplify ACL management
 * @package AppBundle\Security
 */
class AclManager
{
    /**
     * @var MutableAclProviderInterface
     */
    private $provider;

    /**
     * AclManager constructor.
     * @param MutableAclProviderInterface $provider
     */
    public function __construct(MutableAclProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Grant a permission
     * @param mixed $entity DomainObject that we are adding the permission to
     * @param UserInterface $user Concerned user
     * @param int           $mask The mask to grant
     * @return mixed
     */
    public function grant($entity, UserInterface $user, $mask = BestWishesMaskBuilder::MASK_EDIT) {
        $acl = $this->getAcl($entity);

        $securityIdentity = UserSecurityIdentity::fromAccount($user);

        $this->addMask($securityIdentity, $mask, $acl);

        return $entity;
    }

    /**
     * Revoke a permission
     *
     * @param mixed         $entity DomainObject that we are revoking the permission for
     * @param UserInterface $user Concerned user
     * @param int|string    $mask The mask to revoke
     * @return $this
     */
    public function revoke($entity, UserInterface $user, $mask = BestWishesMaskBuilder::MASK_EDIT)
    {
        $acl = $this->getAcl($entity);
        $aces = $acl->getObjectAces();

        $securityIdentity = UserSecurityIdentity::fromAccount($user);

        /** @var AuditableEntryInterface $ace */
        foreach ($aces as $index => $ace) {
            if ($securityIdentity->equals($ace->getSecurityIdentity())) {
                $this->revokeMask($index, $acl, $ace, $mask);
            }
        }

        $this->provider->updateAcl($acl);

        return $this;
    }



    /**
     * Get ACL of an entiry
     * @param mixed $entity Entity to get the ACL from
     * @return \Symfony\Component\Security\Acl\Model\AclInterface|MutableAclInterface
     */
    private function getAcl($entity)
    {
        $aclProvider = $this->provider;
        $objectIdentity = ObjectIdentity::fromDomainObject($entity);
        try {
            $acl = $aclProvider->createAcl($objectIdentity);
        } catch (\Exception $e) {
            $acl = $aclProvider->findAcl($objectIdentity);
        }

        return $acl;
    }

    /**
     * Remove a mask
     * @param int                     $index ACE index to remove the mask from
     * @param MutableAclInterface     $acl ACL to update
     * @param AuditableEntryInterface $ace ACE to remove the mask from
     * @param int                     $mask Mask to remove
     * @return $this
     */
    private function revokeMask($index, MutableAclInterface $acl, AuditableEntryInterface $ace, $mask)
    {
        $acl->updateObjectAce($index, $ace->getMask() & ~$mask);

        return $this;
    }

    /**
     * Add a mask
     *
     * @param SecurityIdentityInterface $securityIdentity
     * @param integer|string            $mask
     * @param MutableAclInterface   $acl ACL to update
     * @return $this
     */
    private function addMask(SecurityIdentityInterface $securityIdentity, $mask, MutableAclInterface $acl)
    {
        $acl->insertObjectAce($securityIdentity, $mask);
        $this->provider->updateAcl($acl);

        return $this;
    }

    /*
    private function addMask($securityIdentity, $mask, MutableAclInterface $acl)
    {
        $acl->updateObjectAce($index, $ace->getMask() & ~$mask);
        $acl->insertObjectAce($securityIdentity, $mask);
        $this->provider->updateAcl($acl);

        return $this;
    }*/
}
