<?php

namespace BestWishes\Repository;

use BestWishes\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array<string, string|string[]> $criteria, ?array<string, string> $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array<string, string|string[]> $criteria, ?array<string, string> $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new \InvalidArgumentException('Instances of "'.User::class.'" are supported only.');
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->flush();
    }
}
