<?php

namespace App\Controller;

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

class MessageController extends AbstractController
{
    public function __construct(
        private MessageService $messageService,
        private MessageRepository $messageRepository
    ) {}

    #[Route('user/dialog/message/forward', name: 'app_message_forward', methods: "POST")]
    public function forwardMessage(Request $request, UserRepository $userRepository)
    {

        $sender = $this->getUser();
        $recipient = $userRepository->find($request->request->get('recipient'));
        $forwardedFrom = $request->request->get('forwardedFrom');
        $content = $request->request->get('content');
        $photoFilename = $request->request->get('photoData');

        $newMessage = $this->messageService->createAndPersist($sender, $recipient, $content, $photoFilename, null, $forwardedFrom);

        if (!$newMessage) {
            return new JsonResponse(['error' => 'Unable to create new message!'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['success' => true, 'message' => $newMessage]);
    }

    #[Route('user/dialog/message/new', name: 'app_new_message', methods: "POST")]
    public function newMessage(Request $request, PublisherInterface $publisher, UserRepository $userRepository): JsonResponse
    {

        // Handle uploaded file with your MessageService
        $uploadedFile = $request->files->get('photoData');
        $photoFilename = null;
        if ($uploadedFile) {
            $photoFilename = $this->messageService->uploadPhoto($uploadedFile);
        }

        // Assume sender and recipient are accessible
        $sender = $this->getUser();
        $recipient = $userRepository->find($request->request->get('recipient'));

        $content = $request->request->get('content');

        $newMessage = $this->messageService->createAndPersist($sender, $recipient, $content, $photoFilename, $request->request->get('replyToMessageId'));

        if (!$newMessage) {
            return new JsonResponse(['error' => 'Unable to create new message!'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $updatedAt = $newMessage->getUpdatedAt() ? $newMessage->getUpdatedAt()->format('H:i') : null;
        $createdAt = $newMessage->getCreatedAt() ? $newMessage->getCreatedAt()->format('H:i') : null;

        $replyToMessage = $newMessage->getReplyToMessage();
        $replyToMessageFormatted = null;
        if ($replyToMessage) {
            $replyToMessageFormatted = [
                'id' => $replyToMessage->getId(),
                'sender' => $replyToMessage->getSender()->getUsername(),
                'content' => $replyToMessage->getContent(),
            ];
        }

        $senderUpdate = new Update(
            '/dialog/user/'.$recipient->getId(),
            json_encode([
                'sender' => $sender->getUsername(),
                'senderAvatar' => $sender->getAvatarFileName(),
                'id' => $newMessage->getId(),
                'content' => $newMessage->getContent(),
                'updatedAt' => $updatedAt,
                'createdAt' => $createdAt,
                'photoData' => $photoFilename,
                'replyToMessage' => $replyToMessageFormatted,
            ])
        );
        $recipientUpdate = new Update(
            '/dialog/user/'.$sender->getId(),
            json_encode([
                'sender' => $sender->getUsername(),
                'senderAvatar' => $sender->getAvatarFileName(),
                'id' => $newMessage->getId(),
                'content' => $newMessage->getContent(),
                'updatedAt' => $updatedAt,
                'createdAt' => $createdAt,
                'photoData' => $photoFilename,
                'replyToMessage' => $replyToMessageFormatted
            ])
        );

        $publisher($senderUpdate);
        $publisher($recipientUpdate);

        return new JsonResponse(['success' => true, 'message' => $newMessage]);
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

        $senderUpdate = new Update(
            '/dialog/user/'.$message->getSender()->getId(),
            json_encode(['edit' => $message->getId(), 'editContent' => $message->getContent(), 'editTimestamp' => $message->getUpdatedAt()->format('H:i')])
        );

        $recipientUpdate = new Update(
            '/dialog/user/'.$message->getRecipient()->getId(),
            json_encode(['edit' => $message->getId(), 'editContent' => $message->getContent(), 'editTimestamp' => $message->getUpdatedAt()->format('H:i')])
        );

        $publisher($senderUpdate);
        $publisher($recipientUpdate);

        return new JsonResponse([
            'success' => true,
            'message' => [
                'content' => $message->getContent(),
                'sender' => $message->getSender()->getUsername(),
                'updatedTimestamp' => $message->getUpdatedAt()->format('H:i')
            ]
        ], Response::HTTP_OK);
    }
}