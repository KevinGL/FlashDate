<?php

namespace App\Controller;

use App\Entity\Session;
use App\Form\SessionsFormType;
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
    public function index(SessionRepository $repo): Response
    {
        if(!$this->getUser())
        {
            return $this->redirectToRoute("app_login");
        }
    
        $sessions = $repo->getNextSessions(in_array("admin", $this->getUser()->getRoles()));

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

        for($i = 0 ; $i < count($sessions) ; $i++)
        {
            $sessions[$i]->img = $imgs[$i % 7];
        }
    
        return $this->render('sessions/index.html.twig', [
            'sessions' => $sessions
        ]);
    }

    #[Route('/sessions/add', name: 'add_session')]
    public function add(EntityManagerInterface $em): Response
    {
        if(!$this->getUser())
        {
            return $this->json(["message" => "Not authenticated"], 401);
        }

        if(!in_array("admin", $this->getUser()->getRoles()))
        {
            return $this->json(["message" => "Not admin"], 403);
        }
    
        $start = new DateTimeImmutable();
        $start = $start->setTime(21, 30, 0);
        $startTS = $start->getTimestamp();

        $end = new DateTimeImmutable();
        $end = $end->setTime(22, 0, 0);
        $endTS = $end->getTimestamp();

        for($i = 0 ; $i < 14 ; $i++)
        {
            $date = new \DateTimeImmutable();
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

    #[Route('/sessions/edit/{id}', name: 'edit_session')]
    public function edit(SessionRepository $repo, EntityManagerInterface $em, Request $req, int $id): Response
    {
        if(!$this->getUser())
        {
            return $this->redirectToRoute("app_login");
        }

        if(!in_array("admin", $this->getUser()->getRoles()))
        {
            return $this->redirectToRoute("app_sessions");
        }    
    
        $session = $repo->find($id);
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
}
