<?php

namespace App\Repository;

use App\Entity\PIReply;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PIReply|null find($id, $lockMode = null, $lockVersion = null)
 * @method PIReply|null findOneBy(array $criteria, array $orderBy = null)
 * @method PIReply[]    findAll()
 * @method PIReply[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PIReplyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PIReply::class);
    }

    // /**
    //  * @return PIReply[] Returns an array of PIReply objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PIReply
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
