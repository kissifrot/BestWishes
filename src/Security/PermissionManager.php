<?php

namespace BestWishes\Security;

use BestWishes\Entity\GiftList;
use BestWishes\Entity\GiftListPermission;
use BestWishes\Entity\User;
use BestWishes\Repository\GiftListPermissionRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Manages gift list permissions
 * Replaces the old AclManager
 */
class PermissionManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly GiftListPermissionRepository $permissionRepository,
    ) {
    }

    /**
     * Grant a permission to a user on a gift list
     */
    public function grant(GiftList $giftList, User $user, string $permission): void
    {
        if ($this->permissionRepository->hasPermission($giftList, $user, $permission)) {
            return;
        }

        $permissionEntity = new GiftListPermission();
        $permissionEntity->setGiftList($giftList);
        $permissionEntity->setUser($user);
        $permissionEntity->setPermission($permission);

        $this->entityManager->persist($permissionEntity);
        $this->entityManager->flush();
    }

    /**
     * Revoke a permission from a user on a gift list
     */
    public function revoke(GiftList $giftList, User $user, string $permission): void
    {
        $permissionEntity = $this->permissionRepository->findPermission($giftList, $user, $permission);

        if ($permissionEntity === null) {
            return;
        }

        $this->entityManager->remove($permissionEntity);
        $this->entityManager->flush();
    }

    /**
     * Exchange a permission between two users
     */
    public function exchangePerms(GiftList $giftList, User $newGranted, User $oldGranted, string $permission): void
    {
        $this->grant($giftList, $newGranted, $permission);
        $this->revoke($giftList, $oldGranted, $permission);
    }

    /**
     * Check if a user has a specific permission
     */
    public function hasPermission(GiftList $giftList, User $user, string $permission): bool
    {
        return $this->permissionRepository->hasPermission($giftList, $user, $permission);
    }

    /**
     * Get all permissions for a user on a gift list
     *
     * @return string[]
     */
    public function getUserPermissions(GiftList $giftList, User $user): array
    {
        return $this->permissionRepository->getUserPermissions($giftList, $user);
    }

    /**
     * Remove all permissions for a user on a gift list
     */
    public function removeAllPermissions(GiftList $giftList, User $user): void
    {
        $this->permissionRepository->removeAllPermissions($giftList, $user);
    }

    /**
     * Get all users with a specific permission on a gift list
     *
     * @return User[]
     */
    public function getUsersWithPermission(GiftList $giftList, string $permission): array
    {
        return $this->permissionRepository->getUsersWithPermission($giftList, $permission);
    }
}
