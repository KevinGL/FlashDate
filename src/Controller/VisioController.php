<?php

namespace App\Controller;

use App\Repository\DatyRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SessionRepository;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class VisioController extends AbstractController
{
    #[Route('/visio/session/{id_session}', name: 'visio_session')]
    public function index(int $id_session, DatyRepository $repo, SessionRepository $sessionRepo, ParticipantRepository $partRepo): Response
    {
        if(!$this->getUser())
        {
            return $this->redirectToRoute("app_login");
        }

        $now = new \DateTimeImmutable();
        $now = $now->setTimezone(new DateTimeZone('Europe/Paris'));
    
        $session = $sessionRepo->find($id_session);

        if(!$session)
        {
            return $this->redirectToRoute("app_sessions");
        }

        if($session->getEndAt()->getTimeStamp() < $now->getTimeStamp())
        {
            return $this->redirectToRoute("app_sessions");                  //Session expirée
        }

        $parts = $partRepo->findByUser($this->getUser());
        $part = array_filter($parts, function ($p) use ($session) {
            return $p->getSession() === $session;
        })[0];
        $daties = $repo->findByPart($part);

        //dd($daties);
    
        return $this->render('visio/daties.html.twig', [
            'daties' => $daties,
        ]);
    }

    #[Route('/visio/daty/{id_daty}', name: 'visio_daty')]
    public function visio(int $id_daty, DatyRepository $repo): Response
    {
        if(!$this->getUser())
        {
            return $this->redirectToRoute("app_login");
        }
    
        $daty = $repo->find($id_daty);

        if(!$daty)
        {
            return $this->redirectToRoute("app_sessions");
        }

        if($daty->getPart1()->getUser()->getId() !== $this->getUser()->getId() && $daty->getPart2()->getUser()->getId() !== $this->getUser()->getId())
        {
            return $this->redirectToRoute("app_sessions");      //N'appartient pas à ce daty
        }

        $now = new \DateTimeImmutable();
        $now = $now->setTimezone(new DateTimeZone('Europe/Paris'));

        $startAt = $daty->getStartAt()->getTimeStamp();
        $endAt = $daty->getEndAt()->getTimeStamp();
        
        if($now->getTimestamp() < $startAt || $now->getTimestamp() > $endAt)
        {
            return $this->redirectToRoute("app_sessions");      //Expiré
        }

        $uidClient = hash("sha256", $daty->getId() . '-' . $this->getUser()->getId());
        $uid = hash("sha256", $daty->getId());

        $initiator = true;
        if($daty->getPart2()->getUser()->getId() === $this->getUser()->getId())
        {
            $initiator = false;
        }
    
        return $this->render('visio/visio.html.twig', [
            'daty' => $daty,
            'ws_url' => $_ENV['WEB_SOCKET_URL'],
            'uidClient' => $uidClient,
            'uid' => $uid,
            'initiator' => $initiator
        ]);
    }
}
