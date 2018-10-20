<?php

namespace BestWishes\Security\Core;

use BestWishes\Entity\User;
use BestWishes\Security\Acl\Permissions\BestWishesMaskBuilder;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Exception\NoAceFoundException;
use Symfony\Component\Security\Acl\Model\AclProviderInterface;

class BestWishesSecurityContext
{
    /**
     * @var array
     */
    private $aclCache;
    private $aclProvider;

    public function __construct(AclProviderInterface $aclProvider)
    {
        $this->aclProvider = $aclProvider;
        $this->aclCache = [];
    }

    /**
     * @param      $mask
     * @param      $object
     * @param User $user
     * @return bool
     */
    public function isGranted($mask, $object, User $user)
    {
        $objectIdentity = ObjectIdentity::fromDomainObject($object);
        $securityIdentity = UserSecurityIdentity::fromAccount($user);

        $aclCacheId = (string)$objectIdentity;

        if (isset($this->aclCache[$aclCacheId])) {
            $acl = $this->aclCache[$aclCacheId];
        } else {
            try {
                $acl = $this->aclProvider->findAcl($objectIdentity, [$securityIdentity]);
                $this->aclCache[$aclCacheId] = $acl;
            } catch (AclNotFoundException $e) {
                return false;
            }
        }

        if (!\is_int($mask)) {
            $builder = new BestWishesMaskBuilder();
            $builder->add($mask);

            $mask = $builder->get();
        }

        try {
            return $acl->isGranted([$mask], [$securityIdentity], false);
        } catch (NoAceFoundException $e) {
            return false;
        }
    }
}
