<?php

namespace App\Repository;

use App\Entity\Session;
use DateError;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Session>
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    //    /**
    //     * @return Session[] Returns an array of Session objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Session
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function getNextSessions(): Array
    {
        $now = new \DateTime('now', new \DateTimeZone('Europe/Paris'));

        $res = $this->createQueryBuilder('s')
            ->where("s.startAt >= :now")
            ->setParameter('now', $now)
            ->getQuery()
            ->getResult()
        ;

        foreach($res as $r)
        {
            $start = $r->getStartAt();
            $start = $start->setTimeZone(new \DateTimeZone('Europe/Paris'));
            $r->setStartAt($start);

            $end = $r->getEndAt();
            $end = $end->setTimeZone(new \DateTimeZone('Europe/Paris'));
            $r->setEndAt($end);
        }
    
        return $res;
    }

    public function getLastDate()
    {
        $res = $this->createQueryBuilder('s')
            ->orderBy("s.startAt", "DESC")
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    
        return $res ? $res->getStartAt() : new DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
    }
}
