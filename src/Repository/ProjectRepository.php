<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Project>
 *
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }


    public function findByProjectsByAdminId(int $adminId): array
    {

        
        return $this->createQueryBuilder('p')
            ->andWhere('p.admin = :adminId')
            ->setParameter('adminId', $adminId)
            ->getQuery()
            ->getResult();
    }

   

    public function countProjectByAdmin(int $adminId): int
    {
        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->andWhere('p.admin = :adminId')
            ->setParameter('adminId', $adminId)
            ->getQuery()
            ->getSingleScalarResult();
    }


    public function countCollaboratorsByAdminId(int $adminId): int
    {
        return $this->createQueryBuilder('p')
            ->select('count(DISTINCT c.id)')
            ->innerJoin('p.collaborator', 'c')
            ->andWhere('p.admin = :adminId')
            ->setParameter('adminId', $adminId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countEventsByAdminId(int $adminId): int
    {
        return $this->createQueryBuilder('p')
            ->select('count( c.id)')
            ->innerJoin('p.events', 'c')
            ->andWhere('p.admin = :adminId')
            ->setParameter('adminId', $adminId)
            ->getQuery()
            ->getSingleScalarResult();
    }


    //    /**
    //     * @return Project[] Returns an array of Project objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Project
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
