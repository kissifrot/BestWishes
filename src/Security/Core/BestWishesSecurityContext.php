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
    /** @var array<mixed> */
    private array $aclCache = [];

    public function __construct(private readonly AclProviderInterface $aclProvider)
    {
    }

    public function isGranted(mixed $mask, mixed $object, User $user): bool
    {
        $objectIdentity = ObjectIdentity::fromDomainObject($object);
        $securityIdentity = UserSecurityIdentity::fromAccount($user);

        $aclCacheId = (string) $objectIdentity;

        if (isset($this->aclCache[$aclCacheId])) {
            $acl = $this->aclCache[$aclCacheId];
        } else {
            try {
                $acl = $this->aclProvider->findAcl($objectIdentity, [$securityIdentity]);
                $this->aclCache[$aclCacheId] = $acl;
            } catch (AclNotFoundException) {
                return false;
            }
        }

        if (!\is_int($mask)) {
            $builder = new BestWishesMaskBuilder();
            $builder->add($mask);

            $mask = $builder->get();
        }

        try {
            return $acl->isGranted([$mask], [$securityIdentity]);
        } catch (NoAceFoundException) {
            return false;
        }
    }
}
