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

    private function slugToDate(string $slug): string
    {
        $params = explode('-', $slug);
        return $params[0] . '-' . $params[1] . '-' . $params[2] . ' ' . $params[3] . ':' . $params[4] . ':' . $params[5];
    }

    public function findIdBySlug(string $slug): ?Session
    {
        $date = $this->slugToDate($slug);        
        $sessionDate = new \DateTime($date);

        return $this->createQueryBuilder('s')
            ->where("s.startAt = :date")
            ->setParameter('date', $sessionDate)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findBySlug(string $slug): ?Session
    {
        $date = $this->slugToDate($slug);        
        $sessionDate = new \DateTime($date);

        return $this->createQueryBuilder('s')
            ->where("s.startAt = :date")
            ->setParameter('date', $sessionDate)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getNextSessions(): Array
    {
        $now = new \DateTime('now', new \DateTimeZone('Europe/Paris'));

        $res = $this->createQueryBuilder('s')
            ->where("s.startAt >= :now")
            ->setParameter('now', $now)
            ->getQuery()
            ->getResult()
        ;

        /*foreach($res as $r)
        {
            $start = $r->getStartAt();
            $start = $start->setTimeZone(new \DateTimeZone('Europe/Paris'));
            $r->setStartAt($start);

            $end = $r->getEndAt();
            $end = $end->setTimeZone(new \DateTimeZone('Europe/Paris'));
            $r->setEndAt($end);
        }*/
    
        return $res;
    }

    public function getNextSession(): ?Session
    {
        $now = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
    
        $res = $this->createQueryBuilder('s')
            ->where("s.startAt >= :now")
            ->setMaxResults(1)
            ->orderBy("s.startAt", "ASC")
            ->setParameter('now', $now)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        $start = $res->getStartAt();
        $start = $start->setTimeZone(new \DateTimeZone('Europe/Paris'));
        $res->setStartAt($start);

        $end = $res->getEndAt();
        $end = $end->setTimeZone(new \DateTimeZone('Europe/Paris'));
        $res->setEndAt($end);

        return $res;
    }

    public function getLastDate()
    {
        $lastSession = $this->createQueryBuilder('s')
            ->orderBy("s.startAt", "DESC")
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        if(!$lastSession)
        {
            $sessionDate = new DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
            $sessionDate = $sessionDate->modify('+ 1 days');
            $sessionDate = $sessionDate->setTime(21, 30, 0);
            $utcDate = $sessionDate->setTimezone(new \DateTimeZone('UTC'));

            return $utcDate;
        }

        $date = new \DateTimeImmutable();
        $date = $date->setTimestamp($lastSession->getStartAt()->getTimestamp());
        $date = $date->modify('+ 1 days');
    
        return $date;
    }

    public function getByDate(string $date): ?Session
    {
        return $this->createQueryBuilder('s')
            ->where('s.startAt = :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
