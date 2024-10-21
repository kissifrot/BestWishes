<?php

namespace BestWishes\Security;

use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Model\AuditableEntryInterface;
use Symfony\Component\Security\Acl\Model\MutableAclInterface;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Model\SecurityIdentityInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class to simplify ACL management
 */
class AclManager
{
    public function __construct(private readonly MutableAclProviderInterface $provider)
    {
    }

    /**
     * Grant a permission
     * @param mixed         $entity DomainObject that we are adding the permission to
     * @param UserInterface $user Concerned user
     * @param int           $mask The mask to grant
     */
    public function grant(mixed $entity, UserInterface $user, int $mask = MaskBuilder::MASK_EDIT): mixed
    {
        $acl = $this->getAcl($entity);

        $securityIdentity = UserSecurityIdentity::fromAccount($user);

        $this->addMask($securityIdentity, $mask, $acl);

        return $entity;
    }

    /**
     * Shortcut method to exchange a permission
     * @param mixed         $entity DomainObject that we are adding the permission to
     * @param UserInterface $newGranted User we're granting the permission to
     * @param UserInterface $oldGranted User we're revoking the permission from
     * @param int           $mask The mask to exchange
     */
    public function exchangePerms(mixed $entity, UserInterface $newGranted, UserInterface $oldGranted, int $mask = MaskBuilder::MASK_EDIT): void
    {
        // Add the correct ACL for the new owner
        $this->grant($entity, $newGranted, $mask);
        // Remove the owner ACL for the old owner
        $this->revoke($entity, $oldGranted, $mask);
    }


    /**
     * Revoke a permission
     *
     * @param mixed         $entity DomainObject that we are revoking the permission for
     * @param UserInterface $user Concerned user
     * @param int           $mask The mask to revoke
     * @return $this
     */
    public function revoke(mixed $entity, UserInterface $user, int $mask = MaskBuilder::MASK_EDIT): self
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
     * Get ACL of an entry
     * @param mixed $entity Entity to get the ACL from
     */
    private function getAcl(mixed $entity): MutableAclInterface
    {
        $aclProvider = $this->provider;
        $objectIdentity = ObjectIdentity::fromDomainObject($entity);
        try {
            $acl = $aclProvider->createAcl($objectIdentity);
        } catch (\Exception) {
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
     */
    private function revokeMask(int $index, MutableAclInterface $acl, AuditableEntryInterface $ace, int $mask): void
    {
        $acl->updateObjectAce($index, $ace->getMask() & ~$mask);
    }

    /**
     * Add a mask
     *
     * @param MutableAclInterface $acl ACL to update
     */
    private function addMask(SecurityIdentityInterface $securityIdentity, int $mask, MutableAclInterface $acl): void
    {
        $acl->insertObjectAce($securityIdentity, $mask);
        $this->provider->updateAcl($acl);
    }
}
