<?php

namespace BestWishes\Repository;

use BestWishes\Entity\GiftList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\RegistryInterface;

class GiftListRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, GiftList::class);
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findFullBySlug(string $slug): ?GiftList
    {
        return $this->createQueryBuilder('l')
            ->where('l.slug = :slug')
            ->leftJoin('l.categories', 'c')
            ->leftJoin('c.gifts', 'g', Join::WITH, 'g.received = :received')
            ->addSelect('c', 'g')
            ->setParameter('slug', $slug)
            ->setParameter('received', false)
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 600, 'giftlist_full_slug_' . $slug)
            ->getOneOrNullResult();
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
     * @throws \Doctrine\ORM\NonUniqueResultException
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
            ->useQueryCache(true)
            ->useResultCache(true, 600, 'giftlist_full_' . $id)
            ->getOneOrNullResult();
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
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
            ->useQueryCache(true)
            ->useResultCache(true, 600, 'giftlist_full_surpr_excl_' . $id)
            ->setResultCacheLifetime(600)
            ->getOneOrNullResult();
    }
}
