<?php

namespace App\Controller;

use App\Entity\Participant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Message\MatchMakerMessage;
use App\Repository\DatyRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SessionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig',
        [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/test_cron')]
    public function testCron(MessageBusInterface $bus): Response
    {
        $bus->dispatch(new MatchMakerMessage());

        return new Response('Message envoyé manuellement dans la file !');
    }

    #[Route('/see_daties')]
    public function seeDaties(ParticipantRepository $partRepo, SessionRepository $sessionRepo, DatyRepository $repo): Response
    {
        $part = $partRepo->findByUserAndSession($this->getUser(), $sessionRepo->getNextSession());
        $daties = $repo->findByPart($part);
        dd($daties);

        return new Response('Message envoyé manuellement dans la file !');
    }

    /*#[Route('/add_part')]
    public function addPart(SessionRepository $sessionsRepo, UserRepository $userRepo, EntityManagerInterface $em): Response
    {
        $session = $sessionsRepo->getNextSession();
        $users = $userRepo->findAll();

        foreach($users as $user)
        {
            if(rand(0, 1) === 1)
            {
                $part = new Participant;
                $part->setSession($session);
                $part->setUser($user);

                $em->persist($part);
                $em->flush();
            
                $session->addParticipant($part);
            }
        }

        $em->persist($session);
        $em->flush();

        return new Response('Participants ajoutés !');
    }*/
}
