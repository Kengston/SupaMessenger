<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

class MessageController extends AbstractController
{
    private $userRepository;

    public function __contruct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    #[Route('/messages', name: 'app_message')]
    public function sendMessage(Request $request, EntityManagerInterface $entityManager): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);

        // Handle request
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get the authenticated user
            $user = $this->getUser();

            // Set user as the sender
            $message->setSender($user);
            $message->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($message);
            $entityManager->flush();

            // Redirect or show some success messages
            return $this->redirectToRoute('app_user');
        }

        return $this->render('messages/message.html.twig', [
            'messageForm' => $form->createView(),
        ]);
    }


}