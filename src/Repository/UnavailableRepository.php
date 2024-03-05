<?php

namespace App\Repository;

use App\Entity\Unavailable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Unavailable>
 *
 * @method Unavailable|null find($id, $lockMode = null, $lockVersion = null)
 * @method Unavailable|null findOneBy(array $criteria, array $orderBy = null)
 * @method Unavailable[]    findAll()
 * @method Unavailable[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UnavailableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Unavailable::class);
    }

    //    /**
    //     * @return Unavailable[] Returns an array of Unavailable objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Unavailable
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
