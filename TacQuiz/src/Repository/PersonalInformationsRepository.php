<?php

namespace App\Repository;

use App\Entity\PersonalInformations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PersonalInformations|null find($id, $lockMode = null, $lockVersion = null)
 * @method PersonalInformations|null findOneBy(array $criteria, array $orderBy = null)
 * @method PersonalInformations[]    findAll()
 * @method PersonalInformations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonalInformationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonalInformations::class);
    }

    // /**
    //  * @return PersonalInformations[] Returns an array of PersonalInformations objects
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
    public function findOneBySomeField($value): ?PersonalInformations
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
