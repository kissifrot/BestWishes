<?php

namespace AppBundle\Security\Core;


use AppBundle\Entity\User;
use AppBundle\Security\Acl\Permissions\BestWishesMaskBuilder;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Exception\NoAceFoundException;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;

class BestWishesSecurityContext
{
    /**
     * @var array
     */
    private $aclCache;

    /**
     * @var MutableAclProviderInterface
     */
    private $aclProvider;

    /**
     * @param MutableAclProviderInterface $aclProvider
     */
    public function __construct(MutableAclProviderInterface $aclProvider)
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

        if (!is_int($mask)) {
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
