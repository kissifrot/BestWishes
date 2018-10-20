<?php

namespace BestWishes\Repository;

use BestWishes\Entity\Category;
use BestWishes\Entity\GiftList;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/**
 * Class CategoryRepository
 */
class CategoryRepository extends EntityRepository
{
    /**
     * @param GiftList $list
     * @return mixed
     */
    public function findByList(GiftList $list)
    {
        return $this->createQueryBuilder('c')
            ->where('c.list = :list')
            ->setParameter('list', $list)
            ->getQuery()
            ->useQueryCache(true)
            ->getResult();
    }

    /**
     * @param int $id
     * @return Category|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findById(int $id): ?Category
    {
        return $this->createQueryBuilder('c')
            ->where('c.id = :id')
            ->leftJoin('c.gifts', 'g', Join::WITH, 'g.received = :received')
            ->addSelect('g')
            ->setParameter('id', $id)
            ->setParameter('received', false)
            ->getQuery()
            ->useQueryCache(true)
            ->getOneOrNullResult();
    }

    /**
     * @param int $id
     * @return Category|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findFullById(int $id): ?Category
    {
        return $this->createQueryBuilder('c')
            ->where('c.id = :id')
            ->leftJoin('c.gifts', 'g', Join::WITH, 'g.received = :received')
            ->leftJoin('c.list', 'l')
            ->addSelect('g', 'l')
            ->setParameter('id', $id)
            ->setParameter('received', false)
            ->getQuery()
            ->useQueryCache(true)
            ->getOneOrNullResult();
    }

    /**
     * @param int $id
     * @return Category|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findFullSurpriseExcludedById(int $id): ?Category
    {
        return $this->createQueryBuilder('c')
            ->where('c.id = :id')
            ->leftJoin('c.gifts', 'g', Join::WITH, 'g.received = :received AND g.surprise = :surprise')
            ->leftJoin('c.list', 'l')
            ->addSelect('g', 'l')
            ->setParameter('id', $id)
            ->setParameter('received', false)
            ->setParameter('surprise', false)
            ->getQuery()
            ->useQueryCache(true)
            ->getOneOrNullResult();
    }
}
