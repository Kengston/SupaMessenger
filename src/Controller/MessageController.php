<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;
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

    #[Route('/user/dialog/{id}', name: 'app_dialog')]
    public function dialog(UserRepository $userRepository, MessageRepository $messageRepository,
                           int $id, EntityManagerInterface $entityManager, Request $request,
                           HubInterface $hub): Response
    {
        $newMessage = new Message();
        $form = $this->createForm(MessageType::class, $newMessage);

        $selectedUser = $userRepository->find($id);

        $currentUser = $this->getUser();
        $messages = $messageRepository->findDialogMessages($currentUser, $selectedUser);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set user as the sender
            $newMessage->setSender($currentUser);
            $newMessage->setRecipient($selectedUser);
            $newMessage->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($newMessage);
            $entityManager->flush();

            // Publish real-time update to Mercure hub
                $update = new Update(
                    '/dialog/'.$id, // Use a unique topic for this dialog
                    json_encode(['message' => $newMessage->getContent()])
                );
                $hub->publish($update);

            // Redirect or show some success messages
            return $this->redirectToRoute('app_dialog', ['id' => $id]);
        }

        return $this->render('messages/dialog.html.twig', [
            'selectedUser' => $selectedUser,
            'messages' => $messages,
            'messageForm' => $form->createView(),
            'id' => $id
        ]);
    }
}