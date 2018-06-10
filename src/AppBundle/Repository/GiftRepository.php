<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Gift;
use Doctrine\ORM\EntityRepository;

/**
 * Class GiftRepository
 * @package AppBundle\Entity
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
            ->getOneOrNullResult();
    }
}
