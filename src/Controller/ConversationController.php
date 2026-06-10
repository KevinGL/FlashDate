<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\ConversationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\HubInterface;

final class ConversationController extends AbstractController
{
    #[Route('/conversations', name: 'app_conversations')]
    public function index(ConversationRepository $repo, UserRepository $userRepo): Response
    {
        if(!$this->getUser())
        {
            return $this->redirectToRoute("app_login");
        }

        $conversations = $repo->getConversations($this->getUser());

        foreach($conversations as $conv)
        {
            if($conv->getUser1Id() !== $this->getUser())
            {
                $conv->interlocutor = $userRepo->findNameById($conv->getUser1Id());
            }

            else
            {
                $conv->interlocutor = $userRepo->findNameById($conv->getUser2Id());
            }
        }
    
        return $this->render('conversation/index.html.twig',
        [
            'conversations' => $conversations
        ]);
    }

    #[Route('/conversations/add/{id1}/{id2}', name: 'add_conversation')]
    public function add(UserRepository $userRepo, EntityManagerInterface $em, int $id1, int $id2): Response
    {
        if(!$this->getUser())
        {
            return $this->json(["message" => "Not authenticated"], 401);
        }

        if(!in_array("ROLE_ADMIN", $this->getUser()->getRoles()))
        {
            return $this->json(["message" => "Forbidden"], 403);
        }

        $user1 = $userRepo->find($id1);
        if(!$user1)
        {
            return $this->json(["message" => "$id1 not found"], 404);
        }

        $user2 = $userRepo->find($id2);
        if(!$user2)
        {
            return $this->json(["message" => "$id2 not found"], 404);
        }

        $conv = new Conversation();
        $conv->setUser1Id($user1);
        $conv->setUserId2($user2);
        $conv->setSlug(hash("sha256", $id1 . '-' . $id2));

        try
        {
            $em->persist($conv);
            $em->flush();
        }
        catch(Exception $e)
        {
            return $this->json(["message" => $e->getMessage()], 500);
        }

        return $this->json(["message", "ok"], 200);
    }

    #[Route('/conversations/view/{slug}', name: 'get_conversation')]
    public function view(ConversationRepository $repo, UserRepository $userRepo, string $slug, Request $req, HubInterface $hub): Response
    {
        if(!$this->getUser())
        {
            return $this->redirectToRoute("app_login");
        }

        $conv = $repo->findBySlug($slug);

        if($conv->getUser1Id() !== $this->getUser() && $conv->getUser2Id() != $this->getUser())
        {
            return $this->redirectToRoute("app_conversations");
        }

        if($conv->getUser1Id() !== $this->getUser())
        {
            $conv->interlocutor = $userRepo->findNameById($conv->getUser1Id());
        }

        else
        {
            $conv->interlocutor = $userRepo->findNameById($conv->getUser2Id());
        }

        return $this->render("conversation/view.html.twig", ["conv" => $conv, "ws_url" => $_ENV["WEB_SOCKET_URL"], "userId" => $this->getUser()->getId(), "username" => $this->getUser()->getUsername()]);
    }
}
