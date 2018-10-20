<?php

namespace BestWishes\Repository;

use BestWishes\Entity\Gift;
use Doctrine\ORM\EntityRepository;

/**
 * Class GiftRepository
 */
class GiftRepository extends EntityRepository
{
    /**
     * @param int $id
     * @return null|Gift
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findFullById(int $id): ?Gift
    {
        return $this->createQueryBuilder('g')
            ->where('g.id = :id')
            ->leftJoin('g.category', 'c')
            ->leftJoin('c.list', 'l')
            ->addSelect('c', 'l')
            ->setParameter('id', $id)
            ->getQuery()
            ->useQueryCache(true)
            ->getOneOrNullResult();
    }

    /**
     * @param int $id
     * @return null|Gift
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findFullSurpriseExcludedById(int $id): ?Gift
    {
        return $this->createQueryBuilder('g')
            ->where('g.id = :id')
            ->andWhere('g.surprise = :surprise')
            ->leftJoin('g.category', 'c')
            ->leftJoin('c.list', 'l')
            ->addSelect('c', 'l')
            ->setParameter('id', $id)
            ->setParameter('surprise', false)
            ->getQuery()
            ->useQueryCache(true)
            ->getOneOrNullResult();
    }
}
