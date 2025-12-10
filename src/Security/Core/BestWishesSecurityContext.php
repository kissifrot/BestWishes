<?php

namespace BestWishes\Security\Core;

use BestWishes\Entity\GiftList;
use BestWishes\Entity\User;
use BestWishes\Security\Voter\GiftListVoter;

/**
 * Service to check permissions for a specific user
 * Compatible with old ACL interface
 */
class BestWishesSecurityContext
{
    public function __construct(private readonly GiftListVoter $giftListVoter)
    {
    }

    /**
     * Check if a user has a permission on an object
     *
     * @param string|int $attribute Permission to check (can be string or old mask for BC)
     * @param mixed $object Object to check permission on (GiftList expected)
     * @param User $user User to check permission for
     */
    public function isGranted(mixed $attribute, mixed $object, User $user): bool
    {
        if (!$object instanceof GiftList) {
            return false;
        }

        // Convert to string if needed (BC with old code using masks)
        if (\is_int($attribute)) {
            return false;
        }

        return $this->giftListVoter->hasPermissionForUser($attribute, $object, $user);
    }
}
