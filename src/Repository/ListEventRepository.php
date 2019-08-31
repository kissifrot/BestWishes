<?php

namespace BestWishes\Repository;

use BestWishes\Entity\ListEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ListEventRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ListEvent::class);
    }

    /**
     * @return ListEvent[]
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
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findBirthdate(): ?ListEvent
    {
        return $this->createQueryBuilder('le')
            ->where('le.type = :type')
            ->setParameter('type', ListEvent::BIRTHDAY_TYPE)
            ->setMaxResults(1)
            ->getQuery()
            ->useQueryCache(true)
            ->getOneOrNullResult();
    }
}
