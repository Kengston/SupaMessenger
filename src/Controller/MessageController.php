<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Service\MessageService;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Attribute\Route;

class MessageController extends AbstractController
{
    private $userRepository;
    private $messageService;

    public function __construct(UserRepository $userRepository, MessageService $messageService)
    {
        $this->userRepository = $userRepository;
        $this->messageService = $messageService;
    }

    #[Route('/user/dialog/{id}', name: 'app_dialog')]
    public function dialog(int $id = null, Request $request, PublisherInterface $publisher): Response
    {
        $form = $this->createForm(MessageType::class, new Message());
        $selectedUser = $id ? $this->userRepository->find($id) : null;
        $currentUser = $this->getUser();
        $messages = $selectedUser ? $this->messageService->getFormattedDialogMessages($currentUser, $selectedUser) : [];

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newMessage = $this->messageService->createAndPersist($currentUser, $selectedUser,
                                                            $form->getData()->getContent());

            // Publish real-time update to Mercure hub
//            $senderUpdate = new Update(
//                '/dialog/user/'.$currentUser->getId(),
//                json_encode(['message' => $newMessage->getContent()])
//            );
//            $recipientUpdate = new Update(
//                '/dialog/user/'.$selectedUser->getId(),
//                json_encode(['message' => $newMessage->getContent()])
//            );
//            $publisher($senderUpdate);
//            $publisher($recipientUpdate);

            // Redirect or show some success messages
            return $this->redirectToRoute('app_dialog', ['id' => $selectedUser->getId()]);
        }

        $users = $this->userRepository->findAll();

        return $this->render('messages/dialog.html.twig', [
            'selectedUser' => $selectedUser,
            'currentUser' => $currentUser,
            'messages' => $messages,
            'users' => $users,
            'messageForm' => $form->createView(),
            'id' => $id
        ]);
    }

    #[Route('/user/dialog/{id}/updates', name: 'fetch_dialog_updates')]
    public function fetchUpdates(int $id): JsonResponse
    {
        $currentUser = $this->getUser();
        $selectedUser = $this->userRepository->find($id);
        $formattedMessages = $this->messageService->getFormattedDialogMessages($currentUser, $selectedUser);

        // Return the messages as JSON response
        return $this->json($formattedMessages);
    }
}