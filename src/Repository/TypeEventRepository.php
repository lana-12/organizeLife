<?php

namespace App\Repository;

use App\Entity\TypeEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
