<?php

namespace BestWishes\Security\Voter;

use BestWishes\Entity\GiftList;
use BestWishes\Entity\User;
use BestWishes\Repository\GiftListPermissionRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

/**
 * Voter for GiftList permissions
 */
class GiftListVoter extends Voter
{
    // Standard permissions
    public const VIEW = 'VIEW';
    public const EDIT = 'EDIT';
    public const DELETE = 'DELETE';
    public const OWNER = 'OWNER';
    public const VIEW_SURPRISES = 'VIEW_SURPRISES';

    // Custom permissions
    public const SURPRISE_ADD = 'SURPRISE_ADD';
    public const ALERT_ADD = 'ALERT_ADD';
    public const ALERT_PURCHASE = 'ALERT_PURCHASE';
    public const ALERT_EDIT = 'ALERT_EDIT';
    public const ALERT_DELETE = 'ALERT_DELETE';

    private const SUPPORTED_ATTRIBUTES = [
        self::VIEW,
        self::EDIT,
        self::DELETE,
        self::OWNER,
        self::VIEW_SURPRISES,
        self::SURPRISE_ADD,
        self::ALERT_ADD,
        self::ALERT_PURCHASE,
        self::ALERT_EDIT,
        self::ALERT_DELETE,
    ];

    public function __construct(
        private readonly GiftListPermissionRepository $permissionRepository,
        private readonly RoleHierarchyInterface $roleHierarchy,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!$subject instanceof GiftList) {
            return false;
        }

        return \in_array($attribute, self::SUPPORTED_ATTRIBUTES, true);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var GiftList $giftList */
        $giftList = $subject;

        if ($attribute === self::VIEW_SURPRISES || $attribute === self::OWNER) {
            return $this->hasPermissionForUser($attribute, $giftList, $user);
        }

        return $this->isAdmin($token->getRoleNames())
            || $this->permissionRepository->hasPermission($giftList, $user, $attribute);
    }

    /**
     * Check permissions for a specific user (used by BestWishesSecurityContext)
     */
    public function hasPermissionForUser(string $attribute, GiftList $giftList, User $user): bool
    {
        return match ($attribute) {
            self::OWNER => $giftList->isOwnedBy($user),
            self::VIEW_SURPRISES => !$giftList->isOwnedBy($user) && (
                $this->isAdmin($user->getRoles())
                || $this->permissionRepository->hasPermission($giftList, $user, self::EDIT)
                || $this->permissionRepository->hasPermission($giftList, $user, self::SURPRISE_ADD)
            ),
            default => $this->permissionRepository->hasPermission($giftList, $user, $attribute),
        };
    }

    /** @param string[] $roles */
    private function isAdmin(array $roles): bool
    {
        return \in_array('ROLE_ADMIN', $this->roleHierarchy->getReachableRoleNames($roles), true);
    }
}
