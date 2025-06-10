<?php

namespace App\Repository;

use App\Entity\TypeEvent;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<TypeEvent>
 *
 * @method TypeEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeEvent|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeEvent[]    findAll()
 * @method TypeEvent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeEvent::class);
    }

    public function findByUser($user): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.user = :user')
            ->setParameter('user', $user)
            ->orderBy('t.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return TypeEvent[] Returns an array of TypeEvent objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?TypeEvent
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
