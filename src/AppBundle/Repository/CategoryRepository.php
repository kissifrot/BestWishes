<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Category;
use AppBundle\Entity\GiftList;
use Doctrine\ORM\EntityRepository;

/**
 * Class CategoryRepository
 * @package AppBundle\Entity
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
            ->andWhere('g.received = :received')
            ->leftJoin('c.gifts', 'g')
            ->addSelect('g')
            ->setParameter('id', $id)
            ->setParameter('received', false)
            ->getQuery()
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
            ->andWhere('g.received = :received')
            ->leftJoin('c.gifts', 'g')
            ->leftJoin('c.list', 'l')
            ->addSelect('g', 'l')
            ->setParameter('id', $id)
            ->setParameter('received', false)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
