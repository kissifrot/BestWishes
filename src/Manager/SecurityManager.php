<?php

namespace BestWishes\Manager;

use BestWishes\Entity\GiftList;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SecurityManager
{
    public function __construct(private readonly Security $security)
    {
    }

    /**
     * Check if current user has specific for a specific GiftList
     */
    public function checkAccess(mixed $attributes, GiftList $list): void
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
