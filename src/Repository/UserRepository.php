<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findBySearch(string $term): array
    {
        $qb = $this->createQueryBuilder('u');

        if (is_numeric($term)) {
            $qb->andWhere('u.id = :id')
               ->setParameter('id', (int) $term);
        } else {
            $qb->andWhere('u.name LIKE :term OR u.email LIKE :term')
               ->setParameter('term', '%' . $term . '%');
        }

        return $qb->orderBy('u.id', 'ASC')
                  ->getQuery()
                  ->getResult();
    }
}
