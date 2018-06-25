<?php

namespace AppBundle\Repository;

use AppBundle\Manager\ListEventManager;
use Doctrine\ORM\EntityRepository;

/**
 * Class ListEventRepository
 * @package AppBundle\Entity
 */
class ListEventRepository extends EntityRepository
{
    /**
     * @return mixed
     */
    public function findAllActive()
    {
        return $this->createQueryBuilder('le')
            ->where('le.active = true')
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 'all_active')
            ->setResultCacheLifetime(3600)
            ->getResult();
    }

    /**
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findBirthdate()
    {
        return $this->createQueryBuilder('le')
            ->where('le.type = :type')
            ->setParameter('type', ListEventManager::BIRTHDAY_TYPE)
            ->setMaxResults(1)
            ->getQuery()
            ->useQueryCache(true)
            ->getOneOrNullResult();
    }
}
