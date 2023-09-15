<?php

namespace App\Repository;

use App\Entity\ElectionResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ElectionResult>
 *
 * @method ElectionResult|null find($id, $lockMode = null, $lockVersion = null)
 * @method ElectionResult|null findOneBy(array $criteria, array $orderBy = null)
 * @method ElectionResult[]    findAll()
 * @method ElectionResult[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ElectionResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ElectionResult::class);
    }

//    /**
//     * @return ElectionResult[] Returns an array of ElectionResult objects
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

//    public function findOneBySomeField($value): ?ElectionResult
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
