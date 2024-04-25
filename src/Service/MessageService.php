<?php

namespace App\Service;

use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MessageService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MessageRepository $messageRepository,
        private ParameterBagInterface $params
    ) {
    }

    private function createMessage(User $sender, User $recipient, string $content, ?string $photoFilename): Message
    {
        $message = new Message();
        $message->setSender($sender);
        $message->setRecipient($recipient);
        $message->setContent($content);
        $message->setCreatedAt(new \DateTimeImmutable());
        $message->setPhotoData($photoFilename);

        return $message;
    }

    public function createAndPersist(User $sender, User $recipient, string $content, ?string $photoFilename): Message
    {
        $newMessage = $this->createMessage($sender, $recipient, $content, $photoFilename);
        $this->entityManager->persist($newMessage);
        $this->entityManager->flush();

        return $newMessage;
    }

    public function uploadPhoto(?UploadedFile $photoData): ?string
    {
        if (!$photoData) {
            return null;
        }

        $photoDataPath = uniqid() . '.' . $photoData->guessExtension();
        $photoData->move($this->params->get('kernel.project_dir') . '/public/uploads', $photoDataPath);

        return $photoDataPath;
    }

    public function updateMessage($message) : void
    {
        $message->setUpdatedAt(new \DateTime());
        $this->entityManager->persist($message);
        $this->entityManager->flush();
    }

    public function deleteMessage(Message $message) : void
    {
        $message->SetDeleted(true);
        $this->entityManager->persist($message);
        $this->entityManager->flush();
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

    public function hasUnreadMessages(User $currentUser, User $otherUser) {
        $unreadMessages = $this->messageRepository->findBy([
            'read' => false,
            'sender' => $otherUser,
            'recipient' => $currentUser,
        ]);
        return count($unreadMessages) > 0;
    }
}