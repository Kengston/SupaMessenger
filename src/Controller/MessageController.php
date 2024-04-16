<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageType;
use App\Form\UserType;
use App\Repository\MessageRepository;
use App\Service\MessageService;
use App\Repository\UserRepository;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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

    private $userService;

    public function __construct(UserRepository $userRepository, MessageService $messageService, MessageRepository $messageRepository, UserService $userService)
    {
        $this->userRepository = $userRepository;
        $this->messageService = $messageService;
        $this->messageRepository = $messageRepository;
        $this->userService = $userService;
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