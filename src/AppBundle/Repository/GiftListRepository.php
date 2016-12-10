<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class GiftListRepository
 * @package AppBundle\Entity
 */
class GiftListRepository extends EntityRepository
{
    public function findFullBySlug($slug)
    {
        return $this->createQueryBuilder('l')
            ->where('l.slug = :slug')
            ->leftJoin('l.categories', 'c')
            ->leftJoin('c.gifts', 'g')
            ->addSelect('c', 'g')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 'giftlist_full_' . $slug)
            ->setResultCacheLifetime(600)
            ->getOneOrNullResult();
    }

    public function findFullById($id)
    {
        return $this->createQueryBuilder('l')
            ->where('l.id = :id')
            ->leftJoin('l.categories', 'c')
            ->leftJoin('c.gifts', 'g')
            ->addSelect('c', 'g')
            ->setParameter('id', $id)
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 'giftlist_full_' . $id)
            ->setResultCacheLifetime(600)
            ->getOneOrNullResult();
    }
}
