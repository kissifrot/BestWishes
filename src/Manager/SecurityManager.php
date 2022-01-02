<?php

namespace BestWishes\Manager;

use BestWishes\Entity\GiftList;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class SecurityManager
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Check if current user has specific for a specific GiftList
     */
    public function checkAccess($attributes, GiftList $list): void
    {
        if (!\is_array($attributes)) {
            $attributes = [$attributes];
        }
        foreach ($attributes as $attribute) {
            if ($this->security->isGranted($attribute, $list)) {
                return;
            }
        }
        throw new AccessDeniedException();
    }
}
