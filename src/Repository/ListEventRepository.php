<?php

namespace BestWishes\Repository;

use BestWishes\Entity\ListEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ListEvent>
 * @method ListEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method ListEvent|null findOneBy(array<string, string|string[]> $criteria, ?array<string, string> $orderBy = null)
 * @method ListEvent[]    findAll()
 * @method ListEvent[]    findBy(array<string, string|string[]> $criteria, ?array<string, string> $orderBy = null, $limit = null, $offset = null)
 */
class ListEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ListEvent::class);
    }

    public function save(ListEvent $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return ListEvent[]
     */
    public function findAllActive(): array
    {
        return $this->createQueryBuilder('le')
            ->where('le.active = true')
            ->getQuery()
            ->enableResultCache(3600, 'all_active')
            ->getResult();
    }

    public function findBirthdate(): ?ListEvent
    {
        try {
            return $this->createQueryBuilder('le')
                ->where('le.type = :type')
                ->setParameter('type', ListEvent::BIRTHDAY_TYPE)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException) {
            return null;
        }
    }
}
