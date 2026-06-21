<?php

namespace App\MessageHandler;

use App\Entity\Daty;
use App\Message\MatchMakerMessage;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
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

        for($i = 0 ; $i < count($men_men) - 1 ; $i += 2)
        {
            $daty = new Daty();
            $daty->setPart1($men_men[$i + 0]);
            $daty->setPart2($men_men[$i + 1]);

            $this->em->persist($daty);
        }

        for($i = 0 ; $i < count($women_women) - 1 ; $i += 2)
        {
            $daty = new Daty();
            $daty->setPart1($women_women[$i + 0]);
            $daty->setPart2($women_women[$i + 1]);

            $this->em->persist($daty);
        }

        $men = array_values(array_filter($women_men, function ($part) {
            return $part->getUser()->getGender() === 'man';
        }));

        $women = array_values(array_filter($women_men, function ($part) {
            return $part->getUser()->getGender() === 'woman';
        }));

        $size = count($men) <= count($women) ? count($men) : count($women);

        for($i = 0 ; $i < $size ; $i++)
        {
            $daty = new Daty();
            $daty->setPart1($men[$i]);
            $daty->setPart2($women[$i]);

            $this->em->persist($daty);
        }

        $this->em->flush();
    }
}
