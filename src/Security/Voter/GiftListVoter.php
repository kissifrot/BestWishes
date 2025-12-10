<?php

namespace BestWishes\Security\Voter;

use BestWishes\Entity\GiftList;
use BestWishes\Entity\User;
use BestWishes\Repository\GiftListPermissionRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

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
        self::SURPRISE_ADD,
        self::ALERT_ADD,
        self::ALERT_PURCHASE,
        self::ALERT_EDIT,
        self::ALERT_DELETE,
    ];

    public function __construct(
        private readonly GiftListPermissionRepository $permissionRepository,
        private readonly Security $security,
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

        // Admins have all rights
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // Check database for specific permission (including for owner)
        return $this->permissionRepository->hasPermission($giftList, $user, $attribute);
    }

    /**
     * Check permissions for a specific user (used by BestWishesSecurityContext)
     */
    public function hasPermissionForUser(string $attribute, GiftList $giftList, User $user): bool
    {
        // Always check database - don't apply admin logic when checking another user's permissions
        return $this->permissionRepository->hasPermission($giftList, $user, $attribute);
    }
}
