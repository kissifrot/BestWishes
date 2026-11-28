<?php

namespace BestWishes\Repository;

use BestWishes\Entity\Gift;
use BestWishes\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Clock\DatePoint;

/**
 * @extends ServiceEntityRepository<Gift>
 * @method Gift|null find($id, $lockMode = null, $lockVersion = null)
 * @method Gift|null findOneBy(array<string, string|string[]> $criteria, ?array<string, string> $orderBy = null)
 * @method Gift[]    findAll()
 * @method Gift[]    findBy(array<string, string|string[]> $criteria, ?array<string, string> $orderBy = null, $limit = null, $offset = null)
 */
class GiftRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gift::class);
    }

    public function save(Gift $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function recordPurchase(Gift $gift, User $buyer, ?string $comment): ?Gift
    {
        $updated = $this->createQueryBuilder('g')
            ->update()
            ->set('g.bought', ':bought')
            ->set('g.buyer', ':buyer')
            ->set('g.purchaseDate', ':date')
            ->set('g.purchaseComment', ':comment')
            ->where('g.id = :id AND g.bought = :notBought')
            ->setParameter('id', $gift->getId())
            ->setParameter('bought', true)
            ->setParameter('notBought', false)
            ->setParameter('buyer', $buyer)
            ->setParameter('date', new DatePoint(), 'date_immutable')
            ->setParameter('comment', $comment)
            ->getQuery()
            ->execute();

        if ($updated !== 1) {
            return null;
        }

        $this->getEntityManager()->detach($gift);

        return $this->find($gift->getId());
    }

    public function remove(Gift $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws NonUniqueResultException
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
