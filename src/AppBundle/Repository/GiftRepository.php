<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class GiftRepository
 * @package AppBundle\Entity
 */
class GiftRepository extends EntityRepository
{
    public function findFullById($id)
    {
        return $this->createQueryBuilder('g')
            ->where('g.id = :id')
            ->leftJoin('g.category', 'c')
            ->leftJoin('c.list', 'l')
            ->addSelect('c', 'l')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
