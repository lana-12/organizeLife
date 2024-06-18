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

    public function getAdminStatistics(int $adminId): array
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addScalarResult('project_count', 'projectsCount');
        $rsm->addScalarResult('user_count', 'collaboratorsCount');
        $rsm->addScalarResult('event_count', 'eventsCount');

        $sql = "
            SELECT 
                COUNT(DISTINCT p.id) AS project_count,
                COUNT(DISTINCT pu.user_id) AS user_count,
                COUNT(DISTINCT e.id) AS event_count
            FROM 
                projects p
            INNER JOIN 
                project_user pu ON p.id = pu.project_id
            LEFT JOIN 
                events e ON p.id = e.project_id
            WHERE 
                p.admin_id = :adminId
        ";

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $query->setParameter('adminId', $adminId);

        return $query->getSingleResult();
    }

    // public function countProjectByAdmin(int $adminId): int
    // {
    //     return $this->createQueryBuilder('p')
    //         ->select('count(p.id)')
    //         ->andWhere('p.admin = :adminId')
    //         ->setParameter('adminId', $adminId)
    //         ->getQuery()
    //         ->getSingleScalarResult();
    // }


    // public function countCollaboratorsByAdminId(int $adminId): int
    // {
    //     return $this->createQueryBuilder('p')
    //         ->select('count(DISTINCT c.id)')
    //         ->innerJoin('p.collaborator', 'c')
    //         ->andWhere('p.admin = :adminId')
    //         ->setParameter('adminId', $adminId)
    //         ->getQuery()
    //         ->getSingleScalarResult();
    // }

    // public function countEventsByAdminId(int $adminId): int
    // {
    //     return $this->createQueryBuilder('p')
    //         ->select('count( c.id)')
    //         ->innerJoin('p.events', 'c')
    //         ->andWhere('p.admin = :adminId')
    //         ->setParameter('adminId', $adminId)
    //         ->getQuery()
    //         ->getSingleScalarResult();
    // }


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
