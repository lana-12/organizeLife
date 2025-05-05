<?php

namespace App\Repository;

use App\Entity\Event;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use DateTime;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }


        public function countByProject(int $projectId): int
        {
            return (int) $this->createQueryBuilder('e')
                ->select('COUNT(e.id)')
                ->where('e.project = :projectId')
                ->setParameter('projectId', $projectId)
                ->getQuery()
                ->getSingleScalarResult();
        }

        public function countCurrentMonthByProject(int $projectId): int
        {
            $start = new DateTimeImmutable('first day of this month 00:00:00');
            $end = new DateTimeImmutable('last day of this month 23:59:59');

            return (int) $this->createQueryBuilder('e')
                ->select('COUNT(e.id)')
                ->where('e.project = :projectId')
                ->andWhere('e.date_event_start >= :start')
                ->andWhere('e.date_event_end <= :end')
                ->setParameter('projectId', $projectId)
                ->setParameter('start', $start)
                ->setParameter('end', $end)
                ->getQuery()
                ->getSingleScalarResult();
        }

        public function findWithRelationsByProject(int $projectId): array
        {
            return $this->createQueryBuilder('e')
                ->leftJoin('e.user', 'u')->addSelect('u')
                ->leftJoin('e.typeEvent', 't')->addSelect('t')
                ->leftJoin('e.project', 'p')->addSelect('p')
                ->where('e.project = :projectId')
                ->setParameter('projectId', $projectId)
                ->getQuery()
                ->getResult();
        }


    //    /**
    //     * @return Event[] Returns an array of Event objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Event
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
