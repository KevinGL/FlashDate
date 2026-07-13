<?php

namespace App\MessageHandler;

use App\Entity\Daty;
use App\Message\MatchMakerMessage;
use App\Repository\SessionRepository;
use BcMath\Number;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Timezone;

#[AsMessageHandler]
final class MatchMakerMessageHandler
{
    private SessionRepository $sessionsRepo;
    private EntityManagerInterface $em;

    public function __construct(SessionRepository $sessionsRepo, EntityManagerInterface $em)
    {
        $this->sessionsRepo = $sessionsRepo;
        $this->em = $em;
    }

    private function sortByPriority(Array &$participants)
    {
        foreach($participants as $part)
        {
            $participations = $part->getUser()->getParticipations()->toArray();
            $datesPart = [];

            foreach($participations as $p)
            {
                $datePart = $p->getSession()->getStartAt();
                $datePart = $datePart->setTime(0, 0, 0);
            
                array_push($datesPart, $datePart->getTimestamp());
            }

            $date = new \DateTimeImmutable();
            $date = $date->modify('-1 week');
            $date = $date->setTime(0, 0, 0);

            $weight = 1.0;
            $sum = 0.0;
            
            for($i = 0 ; $i < 7 ; $i++)
            {
                $hasParticipated = in_array($date->getTimestamp(), $datesPart);

                if($hasParticipated)
                {
                    $sum += $weight;
                }

                $date = $date->modify('+ 1 day');
                
                $weight *= 2;
            }

            $part->priority = 1.0 - $sum / 127;
        }

        usort($participants, function ($a, $b) {
            return $a->priority < $b->priority;
        });
    }

    private function calculAge(\DateTime $birth): int
    {
        $today = new \DateTime();

        return $today->diff($birth)->y;
    }

    private function getDistance(string $coord1, string $coord2): float
    {
        $lat1 = explode(',', $coord1)[0];
        $lon1 = explode(',', $coord1)[1];

        $lat2 = explode(',', $coord2)[0];
        $lon2 = explode(',', $coord2)[1];
    
        $earthRadius = 6371.0;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
            
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }

    private function createDaties(array $parts, bool $sameGender): void
    {
        if($sameGender)
        {
            for($i = 0 ; $i < count($parts) ; $i++)
            {
                $user1 = $parts[$i]->getUser();
                $age1 = $this->calculAge($user1->getBirthDate());
                $distMax1 = $user1->getDistanceFilter();
                $ageRange1 = explode('-', $user1->getAgeRange());
                $coord1 = $user1->getCoord();

                for($j = 0 ; $j < count($parts) ; $j++)
                {
                    if($i !== $j)
                    {
                        $user2 = $parts[$j]->getUser();
                        $age2 = $this->calculAge($user2->getBirthDate());
                        $distMax2 = $user2->getDistanceFilter();
                        $ageRange2 = explode('-', $user2->getAgeRange());
                        $coord2 = $user2->getCoord();

                        if($age1 < $ageRange2[0] || $age1 > $ageRange2[1] || $age2 < $ageRange1[0] || $age2 > $ageRange1[1])
                        {
                            continue;
                        }

                        $dist = $this->getDistance($coord1, $coord2);

                        if($dist > $distMax1 || $dist > $distMax2)
                        {
                            continue;
                        }

                        $daty = new Daty();
                        $daty->setPart1($parts[$i]);
                        $daty->setPart2($parts[$j]);

                        $this->em->persist($daty);
                    }
                }
            }
        }

        else
        {
            $men = array_values(array_filter($parts, function ($part) {
                return $part->getUser()->getGender() === 'man';
            }));

            $women = array_values(array_filter($parts, function ($part) {
                return $part->getUser()->getGender() === 'woman';
            }));

            $size = count($men) <= count($women) ? count($men) : count($women);

            for($i = 0 ; $i < $size ; $i++)
            {
                $user1 = $men[$i]->getUser();
                $age1 = $this->calculAge($user1->getBirthDate());
                $distMax1 = $user1->getDistanceFilter();
                $ageRange1 = explode('-', $user1->getAgeRange());
                $coord1 = $user1->getCoord();

                for($j = 0 ; $j < count($women) ; $j++)
                {
                    $user2 = $women[$j]->getUser();
                    $age2 = $this->calculAge($user2->getBirthDate());
                    $distMax2 = $user2->getDistanceFilter();
                    $ageRange2 = explode('-', $user2->getAgeRange());
                    $coord2 = $user2->getCoord();

                    if($age1 < $ageRange2[0] || $age1 > $ageRange2[1] || $age2 < $ageRange1[0] || $age2 > $ageRange1[1])
                    {
                        continue;
                    }

                    $dist = $this->getDistance($coord1, $coord2);

                    if($dist > $distMax1 || $dist > $distMax2)
                    {
                        continue;
                    }

                    $daty = new Daty();
                    $daty->setPart1($parts[$i]);
                    $daty->setPart2($parts[$j]);

                    $this->em->persist($daty);
                }
            }
        }
    }

    public function __invoke(MatchMakerMessage $message): void
    {
        $this->em->clear();
        $participants = $this->sessionsRepo->getNextSession()->getParticipants()->toArray();

        $men_men = array_filter($participants, function ($part) {
            return $part->getUser()->getGender() === 'man' && $part->getUser()->getSearch() === 'man';
        });

        $women_women = array_filter($participants, function ($part) {
            return $part->getUser()->getGender() === 'woman' && $part->getUser()->getSearch() === 'woman';
        });

        $women_men = array_filter($participants, function ($part) {
            return $part->getUser()->getGender() !== $part->getUser()->getSearch();
        });

        $this->sortByPriority($men_men);
        $this->sortByPriority($women_women);
        $this->sortByPriority($women_men);

        $this->createDaties($men_men, true);
        $this->createDaties($women_women, true);
        $this->createDaties($women_men, false);

        $this->em->flush();
    }
}
