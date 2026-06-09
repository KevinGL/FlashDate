<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Session;
use App\Form\SessionsFormType;
use App\Repository\ParticipantRepository;
use App\Repository\SessionRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SessionsController extends AbstractController
{
    #[Route('/sessions', name: 'app_sessions')]
    public function index(SessionRepository $sessionsRepo, ParticipantRepository $partRepo): Response
    {
        if(!$this->getUser())
        {
            return $this->redirectToRoute("app_login");
        }
    
        $sessions = $sessionsRepo->getNextSessions();

        if(!in_array("ROLE_ADMIN", $this->getUser()->getRoles()))
        {
            $sessions = array_filter($sessions, function ($item) {
                return $item->isActive();
            });
        }

        $imgs =
        [
            "/img/pexels-beniam-447198297-19480709.jpg",
            "/img/pexels-cottonbro-9665611.jpg",
            "/img/pexels-elletakesphotos-1058918.jpg",
            "/img/pexels-etoile-3061668-6496368.jpg",
            "/img/pexels-marek-piwnicki-3907296-13328054.jpg",
            "/img/pexels-patrick-dziggel-2160051696-36548504.jpg",
            "/img/pexels-pavel-danilyuk-5858038.jpg"
        ];

        $index = 0;
        foreach($sessions as $session)
        {
            $session->img = $imgs[$index % 7];
            $session->bookable = !$partRepo->sessionBooked($this->getUser()->getId(), $session->getId());

            $index++;
        }
    
        return $this->render('sessions/index.html.twig', [
            'sessions' => $sessions
        ]);
    }

    #[Route('/sessions/add', name: 'add_session')]
    public function add(SessionRepository $repo, EntityManagerInterface $em): Response
    {
        if(!$this->getUser())
        {
            return $this->json(["message" => "Not authenticated"], 401);
        }

        if(!in_array("ROLE_ADMIN", $this->getUser()->getRoles()))
        {
            return $this->json(["message" => "Not admin"], 403);
        }
    
        $start = $repo->getLastDate();
        $start = $start->setTimestamp($start->getTimestamp() + 24 * 3600);
        $start = $start->setTimeZone(new \DateTimeZone('Europe/Paris'));
        $start = $start->setTime(19, 30, 0);
        $startTS = $start->getTimestamp();

        $end = $start;
        $end = $end->setTime(20, 0, 0);
        $endTS = $end->getTimestamp();

        for($i = 0 ; $i < 14 ; $i++)
        {
            $date = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Paris'));
            $date = $date->setTimestamp($startTS);

            $session = new Session();
            $session->setStartAt($date);

            $date = $date->setTimestamp($endTS);
            $session->setEndAt($date);
            
            $session->setIsActive(true);
            $session->setMaxParticipants(50);

            try
            {
                $em->persist($session);
                $em->flush();
            }
            catch(Exception $e)
            {
                return $this->json(["message" => $e->getMessage()], 500);
            }

            $startTS += 24 *3600;
            $endTS += 24 *3600;
        }
    
        return $this->json(["message" => 'Sessions ajoutées'], 200);
    }

    #[Route('/sessions/edit/{slug}', name: 'edit_session')]
    public function edit(SessionRepository $repo, EntityManagerInterface $em, Request $req, string $slug): Response
    {
        if(!$this->getUser())
        {
            return $this->redirectToRoute("app_login");
        }

        if(!in_array("ROLE_ADMIN", $this->getUser()->getRoles()))
        {
            return $this->redirectToRoute("app_sessions");
        }    
    
        $session = $repo->findBySlug($slug);
        $form = $this->createForm(SessionsFormType::class, $session);
        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid())
        {
            $em->persist($session);
            $em->flush();

            return $this->redirectToRoute("app_sessions");
        }
    
        return $this->render('sessions/edit.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/sessions/booking/{slug}', name: 'book_session')]
    public function booking(SessionRepository $repo, ParticipantRepository $partRepo, EntityManagerInterface $em, string $slug): Response
    {
        if(!$this->getUser())
        {
            return $this->json(["message" => "Not authenticated", 401]);
        }

        $id = $repo->findIdBySlug($slug);

        if($partRepo->sessionBooked($this->getUser()->getId(), $id))
        {
            return $this->json(["message" => "Forbidden", 403]);
        }

        $session = $repo->find($id);

        if(count($session->getParticipants()) >= $session->getMaxParticipants())
        {
            return $this->json(["message" => "Forbidden", 403]);
        }
    
        $participant = new Participant();

        $participant->setUserId($this->getUser());
        $participant->setSessionId($session);

        $em->persist($participant);
        $em->flush();
    
        return $this->json(["message" => "ok"], 200);
    }

    #[Route('/sessions/booked', name: 'booked_session')]
    public function booked(ParticipantRepository $partRepo)
    {
        if(!$this->getUser())
        {
            return $this->redirectToRoute("app_login");
        }

        $sessions = [];

        $participants = $partRepo->findByUser($this->getUser()->getId());

        $imgs =
        [
            "/img/pexels-beniam-447198297-19480709.jpg",
            "/img/pexels-cottonbro-9665611.jpg",
            "/img/pexels-elletakesphotos-1058918.jpg",
            "/img/pexels-etoile-3061668-6496368.jpg",
            "/img/pexels-marek-piwnicki-3907296-13328054.jpg",
            "/img/pexels-patrick-dziggel-2160051696-36548504.jpg",
            "/img/pexels-pavel-danilyuk-5858038.jpg"
        ];

        $index = 0;

        foreach($participants as $part)
        {
            $session = $part->getSessionId();    
        
            $start = $session->getStartAt();
            $start = $start->setTimeZone(new \DateTimeZone('Europe/Paris'));
            $startTs = $start->getTimestamp();
            $current = (new \DateTime('now', new \DateTimeZone('Europe/Paris')))->getTimestamp();
            $end = $session->getEndAt();
            $end = $end->setTimeZone(new \DateTimeZone('Europe/Paris'));
            $endTs = $end->getTimestamp();

            $session->joinable = $current >= $startTs && $current <= $endTs;
            $session->img = $imgs[$index % 7];
        
            array_push($sessions, $part->getSessionId());

            $index++;
        }

        return $this->render("sessions/booked.html.twig", ["sessions" => $sessions]);
    }

    #[Route('/sessions/join/{slug}', name: 'join_session')]
    public function join(string $slug)
    {
        //
    }
}
