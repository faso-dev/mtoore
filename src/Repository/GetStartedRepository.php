<?php

namespace App\Repository;

use App\Entity\GetStarted;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GetStarted|null find($id, $lockMode = null, $lockVersion = null)
 * @method GetStarted|null findOneBy(array $criteria, array $orderBy = null)
 * @method GetStarted[]    findAll()
 * @method GetStarted[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GetStartedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GetStarted::class);
    }

    // /**
    //  * @return GetStarted[] Returns an array of GetStarted objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GetStarted
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
