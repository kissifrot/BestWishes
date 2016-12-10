<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class ListEventRepository
 * @package AppBundle\Entity
 */
class ListEventRepository extends EntityRepository
{
    public function findAllActive() {
        return $this->createQueryBuilder('le')
            ->where('le.active = true')
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 'all_active')
            ->setResultCacheLifetime(3600)
            ->getResult();
    }
}
