<?php

namespace App\Service;

use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;

class MessageService
{
    private $messageRepository;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, MessageRepository $messageRepository)
    {
        $this->entityManager = $entityManager;
        $this->messageRepository = $messageRepository;
    }

    public function createAndPersist(User $sender, User $recipient, string $content): Message
    {
        $newMessage = $this->createMessage($sender, $recipient, $content);
        $this->entityManager->persist($newMessage);
        $this->entityManager->flush();

        return $newMessage;
    }

    public function getFormattedDialogMessages(User $currentUser, User $selectedUser): array
    {
        $messages = $this->messageRepository->findDialogMessages($currentUser, $selectedUser);
        $formattedMessages = [];

        foreach ($messages as $message) {
            $formattedMessages[] = [
                'id' => $message->getId(),
                'sender' => $message->getSender()->getUsername(),
                'content' => $message->getContent(),
            ];
        }

        return $formattedMessages;
    }

    public function createMessage(User $sender, User $recipient, string $content): Message
    {
        $message = new Message();
        $message->setSender($sender);
        $message->setRecipient($recipient);
        $message->setContent($content);
        $message->setCreatedAt(new \DateTimeImmutable());

        return $message;
    }
}