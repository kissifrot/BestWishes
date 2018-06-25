<?php

namespace AppBundle\Repository;

use AppBundle\Entity\GiftList;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/**
 * Class GiftListRepository
 * @package AppBundle\Entity
 */
class GiftListRepository extends EntityRepository
{
    /**
     * @param string $slug
     * @return null|GiftList
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findFullBySlug(string $slug): ?GiftList
    {
        return $this->createQueryBuilder('l')
            ->where('l.slug = :slug')
            ->leftJoin('l.categories', 'c')
            ->leftJoin('c.gifts', 'g', Join::WITH, 'g.received = :received')
            ->addSelect('c', 'g')
            ->setParameter('slug', $slug)
            ->setParameter('received', false)
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 'giftlist_full_' . $slug)
            ->setResultCacheLifetime(600)
            ->getOneOrNullResult();
    }

    /**
     * @param int $id
     * @return GiftList|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findFullById(int $id): ?GiftList
    {
        return $this->createQueryBuilder('l')
            ->where('l.id = :id')
            ->leftJoin('l.categories', 'c')
            ->leftJoin('c.gifts', 'g', Join::WITH, 'g.received = :received')
            ->addSelect('c', 'g')
            ->setParameter('id', $id)
            ->setParameter('received', false)
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 'giftlist_full_' . $id)
            ->setResultCacheLifetime(600)
            ->getOneOrNullResult();
    }
}
