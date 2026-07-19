<?php

namespace App\Repository;

use App\Entity\Daty;
use App\Entity\Participant;
use App\Entity\User;
use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Daty>
 */
class DatyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Daty::class);
    }

    //    /**
    //     * @return Daty[] Returns an array of Daty objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Daty
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findByPart(Participant $part): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.part1 = :part')
            ->orWhere('d.part2 = :part')
            ->setParameter('part', $part)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findBySession(Session $session): ?Daty
    {
        return $this->createQueryBuilder('d')
            ->where('d.session = :session')
            ->setParameter('session', $session)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
