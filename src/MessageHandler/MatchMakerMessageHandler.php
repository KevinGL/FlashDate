<?php

namespace App\MessageHandler;

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

    public function __invoke(MatchMakerMessage $message): void
    {
        $this->em->clear();
        $participants = $this->sessionsRepo->getNextSession()->getParticipants()->toArray();

        foreach($participants as $part)
        {
            $priority = 1.0;

            $today = new \DateTime();
            $today->setTime(0, 0, 0);
            $today->setTimezone(new \DateTimeZone('Europe/Paris'));

            $date = new \DateTime();
            $date->setTimestamp($today->getTimestamp());
            $date->setTimezone(new \DateTimeZone('Europe/Paris'));
            $date->modify('-7 days');

            $score = 0.5;

            while(1)
            {
                /*dump($date);
                dump($this->sessionsRepo->getByDate($date->format('Y-m-d 21:30:00')));*/

                $session = $this->sessionsRepo->getByDate($date->format('Y-m-d 21:30:00'));
            
                $date->modify('+1 days');

                $score /= 2;

                if($date->getTimestamp() === $today->getTimestamp())
                {
                    break;
                }
            }

            //dump($priority);
        }
    }
}
