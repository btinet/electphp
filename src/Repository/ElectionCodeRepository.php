<?php

namespace App\Repository;

use App\Entity\ElectionCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ElectionCode>
 *
 * @method ElectionCode|null find($id, $lockMode = null, $lockVersion = null)
 * @method ElectionCode|null findOneBy(array $criteria, array $orderBy = null)
 * @method ElectionCode[]    findAll()
 * @method ElectionCode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ElectionCodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ElectionCode::class);
    }

//    /**
//     * @return ElectionCode[] Returns an array of ElectionCode objects
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

//    public function findOneBySomeField($value): ?ElectionCode
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
