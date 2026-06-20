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
            $session->bookable = !$partRepo->sessionBooked($this->getUser(), $session);

            $index++;
        }
    
        return $this->render('sessions/index.html.twig', [
            'sessions' => $sessions
        ]);
    }

    #[Route('/sessions/add', name: 'add_session')]
    public function add(SessionRepository $repo, EntityManagerInterface $em, Request $req): Response
    {
        if(!$this->getUser())
        {
            return $this->json(["message" => "Not authenticated"], 401);
        }

        if(!in_array("ROLE_ADMIN", $this->getUser()->getRoles()))
        {
            return $this->json(["message" => "Not admin"], 403);
        }

        if($req->headers->get('X-Requested-With') !== 'XMLHttpRequest')
        {
            return $this->json(["message" => "Forbidden", 403]);
        }
    
        $start = $repo->getLastDate();

        $end = new \DateTimeImmutable();
        $end = $end->setTimestamp($start->getTimestamp());
        $end = $end->modify('+ 30 minutes');

        for($i = 0 ; $i < 14 ; $i++)
        {
            $session = new Session();

            $session->setStartAt($start);
            $session->setEndAt($end);
            
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

            $start = $start->modify('+1 days');
            $end = $end->modify('+1 days');
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
    public function booking(Request $req, SessionRepository $repo, ParticipantRepository $partRepo, EntityManagerInterface $em, string $slug): Response
    {
        if(!$this->getUser())
        {
            return $this->json(["message" => "Not authenticated", 401]);
        }

        if($req->headers->get('X-Requested-With') !== 'XMLHttpRequest')
        {
            return $this->json(["message" => "Forbidden"], 403);
        }

        $session = $repo->findIdBySlug($slug);

        if($partRepo->sessionBooked($this->getUser(), $session))
        {
            return $this->json(["message" => "Forbidden", 403]);
        }

        if(count($session->getParticipants()) >= $session->getMaxParticipants())
        {
            return $this->json(["message" => "Forbidden", 403]);
        }
    
        $participant = new Participant();

        $participant->setUser($this->getUser());
        $participant->setSession($session);

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

        $participants = $partRepo->findByUser($this->getUser());

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
            $session = $part->getSession();    
        
            $start = $session->getStartAt();
            $start = $start->setTimeZone(new \DateTimeZone('Europe/Paris'));
            $current = (new \DateTime('now', new \DateTimeZone('Europe/Paris')));
            $end = $session->getEndAt();
            $end = $end->setTimeZone(new \DateTimeZone('Europe/Paris'));

            $session->joinable = $current >= $start && $current <= $end;
            $session->img = $imgs[$index % 7];
        
            array_push($sessions, $part->getSession());

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
