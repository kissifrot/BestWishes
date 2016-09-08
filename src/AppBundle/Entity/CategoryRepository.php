<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class CategoryRepository
 * @package AppBundle\Entity
 */
class CategoryRepository extends EntityRepository
{
    public function findByList(GiftList $list)
    {
        return $this->createQueryBuilder('c')
            ->where('c.list = :list')
            ->setParameter('list', $list)
            ->getQuery()
            ->getResult();
    }

    public function findById($id)
    {
        return $this->createQueryBuilder('c')
            ->where('c.id = :id')
            ->leftJoin('c.gifts', 'g')
            ->addSelect('g')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findFullById($id)
    {
        return $this->createQueryBuilder('c')
            ->where('c.id = :id')
            ->leftJoin('c.gifts', 'g')
            ->leftJoin('c.list', 'l')
            ->addSelect('g', 'l')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
