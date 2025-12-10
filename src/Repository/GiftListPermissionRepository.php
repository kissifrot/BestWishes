<?php

namespace BestWishes\Repository;

use BestWishes\Entity\GiftList;
use BestWishes\Entity\GiftListPermission;
use BestWishes\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GiftListPermission>
 */
class GiftListPermissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GiftListPermission::class);
    }

    /**
     * Find a specific permission for a user on a gift list
     */
    public function findPermission(GiftList $giftList, User $user, string $permission): ?GiftListPermission
    {
        return $this->findOneBy([
            'giftList' => $giftList,
            'user' => $user,
            'permission' => $permission,
        ]);
    }

    /**
     * Check if a user has a specific permission on a gift list
     */
    public function hasPermission(GiftList $giftList, User $user, string $permission): bool
    {
        return $this->findPermission($giftList, $user, $permission) !== null;
    }

    /**
     * Get all permissions for a user on a gift list
     *
     * @return string[]
     */
    public function getUserPermissions(GiftList $giftList, User $user): array
    {
        $permissions = $this->createQueryBuilder('p')
            ->select('p.permission')
            ->where('p.giftList = :giftList')
            ->andWhere('p.user = :user')
            ->setParameter('giftList', $giftList)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        return array_column($permissions, 'permission');
    }

    /**
     * Get all users with a specific permission on a gift list
     *
     * @return User[]
     */
    public function getUsersWithPermission(GiftList $giftList, string $permission): array
    {
        return $this->createQueryBuilder('p')
            ->select('u')
            ->join('p.user', 'u')
            ->where('p.giftList = :giftList')
            ->andWhere('p.permission = :permission')
            ->setParameter('giftList', $giftList)
            ->setParameter('permission', $permission)
            ->getQuery()
            ->getResult();
    }

    /**
     * Remove all permissions for a user on a gift list
     */
    public function removeAllPermissions(GiftList $giftList, User $user): void
    {
        $this->createQueryBuilder('p')
            ->delete()
            ->where('p.giftList = :giftList')
            ->andWhere('p.user = :user')
            ->setParameter('giftList', $giftList)
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }
}
