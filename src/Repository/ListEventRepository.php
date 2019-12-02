<?php

namespace BestWishes\Repository;

use BestWishes\Entity\ListEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class ListEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ListEvent::class);
    }

    /**
     * @return ListEvent[]
     */
    public function findAllActive(): array
    {
        return $this->createQueryBuilder('le')
            ->where('le.active = true')
            ->getQuery()
            ->useQueryCache(true)
            ->enableResultCache(3600, 'all_active')
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
