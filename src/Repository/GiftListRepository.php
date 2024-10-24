<?php

namespace BestWishes\Repository;

use BestWishes\Entity\GiftList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GiftList>
 * @method GiftList|null find($id, $lockMode = null, $lockVersion = null)
 * @method GiftList|null findOneBy(array $criteria, array $orderBy = null)
 * @method GiftList[]    findAll()
 * @method GiftList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GiftListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GiftList::class);
    }

    public function findByCategoryId(int $catId): ?GiftList
    {
        return $this->createQueryBuilder('l')
            ->innerJoin('l.categories', 'c')
            ->where('c.id = :id')
            ->setParameter('id', $catId)
            ->getQuery()
            ->useQueryCache(true)
            ->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findFullById(int $id): ?GiftList
    {
        return $this->createQueryBuilder('l')
            ->where('l.id = :id')
            ->leftJoin('l.categories', 'c')
            ->leftJoin('c.gifts', 'g', Join::WITH, 'g.received = :received')
            ->addSelect('c', 'g')
            ->setParameter('id', $id)
            ->setParameter('received', false)
            ->getQuery()
            ->enableResultCache(600, 'giftlist_full_' . $id)
            ->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findFullSurpriseExcludedById(int $id): ?GiftList
    {
        return $this->createQueryBuilder('l')
            ->where('l.id = :id')
            ->leftJoin('l.categories', 'c')
            ->leftJoin('c.gifts', 'g', Join::WITH, 'g.received = :received AND g.surprise = :surprise')
            ->addSelect('c', 'g')
            ->setParameter('id', $id)
            ->setParameter('received', false)
            ->setParameter('surprise', false)
            ->getQuery()
            ->enableResultCache(600, 'giftlist_full_surpr_excl_' . $id)
            ->getOneOrNullResult();
    }
}
