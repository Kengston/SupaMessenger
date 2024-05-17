<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class DialogService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MessageRepository $messageRepository
    ) {
    }

    public function getFormattedDialogMessages(User $currentUser, User $selectedUser): array
    {
        $messages = $this->messageRepository->findDialogMessages($currentUser, $selectedUser);
        $formattedMessages = [];

        foreach ($messages as $message) {
            if (!$message->getDeleted() and $message !== null) {
                $createdAt = $message->getCreatedAt();
                $createdAtFormatted = $createdAt ? $createdAt->format('H:i') : null;

                $updatedAt = $message->getUpdatedAt();
                $updatedAtFormatted = $updatedAt ? $updatedAt->format('H:i') : null;

                $photoData = $message->getPhotoData();

                $formattedMessages[] = [
                    'id' => $message->getId(),
                    'sender' => $message->getSender()->getUsername(),
                    'content' => $message->getContent(),
                    'createdAt' => $createdAtFormatted,
                    'updatedAt' => $updatedAtFormatted,
                    'photoData' => $photoData
                ];
            }
        }

        return $formattedMessages;
    }

    public function markDialogAsRead(User $currentUser, User $selectedUser): void
    {
        $messages = $this->messageRepository->findBy(
            ['sender' => $selectedUser, 'recipient' => $currentUser]
        );

        foreach ($messages as $message) {
            if (!$message->isRead()) {
                $message->setRead(true);
                $this->entityManager->persist($message);
                $this->entityManager->flush();
            }
        }
    }

    public function getLastMessagesInDialog(User $currentUser, array $users): array
    {
        $lastMessagesInDialog = [];

        foreach ($users as $user) {
            $lastMessage = $this->messageRepository->getLastMessageInDialog($currentUser, $user);
            $lastMessagesInDialog[$user->getId()] = $lastMessage ? $lastMessage->getContent() : null;
        }

        return $lastMessagesInDialog;
    }
}