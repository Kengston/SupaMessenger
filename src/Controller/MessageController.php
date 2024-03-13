<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use App\Service\MessageService;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Attribute\Route;
use function PHPUnit\Framework\throwException;

class MessageController extends AbstractController
{
    private $userRepository;
    private $messageService;

    private $messageRepository;

    public function __construct(UserRepository $userRepository, MessageService $messageService, MessageRepository $messageRepository)
    {
        $this->userRepository = $userRepository;
        $this->messageService = $messageService;
        $this->messageRepository = $messageRepository;
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
            $newMessage = $this->messageService->createAndPersist($currentUser, $selectedUser, $form->getData()->getContent());

            $senderUpdate = new Update(
                '/dialog/user/'.$currentUser->getId(),
                json_encode(['sender' => $currentUser->getUsername(), 'content' => $newMessage->getContent()])
            );
            $recipientUpdate = new Update(
                '/dialog/user/'.$selectedUser->getId(),
                json_encode(['sender' => $currentUser->getUsername(), 'content' => $newMessage->getContent()])
            );
            $publisher($senderUpdate);
            $publisher($recipientUpdate);

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

        return $this->json($formattedMessages);
    }

    #[Route('user/dialog/message/delete/{messageId}', name: 'app_delete_message')]
    public function delete($messageId, PublisherInterface $publisher)
    {
        $message = $this->messageRepository->findMessageById($messageId);

        if (!$message) {
            throw $this->createNotFoundException('Message not found');
        }

        $this->messageService->deleteMessage($message);

        $update = new Update(
            '/dialog/user/'.$message->getRecipient()->getId(),
            json_encode(['delete' => $message->getId()])
        );
        $publisher($update);

        return $this->redirectToRoute('app_dialog', ['id' => $message->getRecipient()->getId()]);
    }

    #[Route('user/dialog/message/edit/{messageId}', name: 'app_edit_message')]
    public function edit($messageId, Request $request, PublisherInterface $publisher) : JsonResponse
    {
        $message = $this->messageRepository->findMessageById($messageId);

        if ($message === null) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Message not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        $message->setContent($data['content']);

        $this->messageService->updateMessage($message);

        $update = new Update(
            '/dialog/user/'.$message->getRecipient()->getId(),
            json_encode(['edit' => $message->getId(), 'editContent' => $message->getContent()])
        );
        $publisher($update);

        return new JsonResponse([
            'success' => true,
            'message' => [
                'content' => $message->getContent(),
                'sender' => $message->getSender()->getUsername(),
            ]
        ], Response::HTTP_OK);
    }
}