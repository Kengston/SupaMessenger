<?php

namespace App\Service;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserService
{
    public function __construct(
        private string $targetDirectory,
        private SluggerInterface $slugger,
        private EntityManagerInterface $entityManager
    ) {
    }


    public function uploadAvatar(User $user, UploadedFile $avatarFileName): void
    {
        $originalAvatarFilename = pathinfo($avatarFileName->getClientOriginalName(), PATHINFO_FILENAME);
        $avatarSafeFilename = $this->slugger->slug($originalAvatarFilename);
        $avatarPath = $avatarSafeFilename.'-'.uniqid().'.'.$avatarFileName->guessExtension();

        try {
            $avatarFileName->move($this->getTargetDirectory(), $avatarPath);
        } catch (FileException $e) {
            throw new \Exception('Unable to upload the avatar: '.$e->getMessage());
        }

        $user->setAvatarFileName($avatarPath);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function updateUserLastMessage(User $user, Message $userMessage)
    {
        $user->setLastMessageContent($userMessage->getContent());
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }

    public function getUsersListSerialized($users): array
    {
        return array_map(function ($user) {
            return [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'avatarFileName' => $user->getAvatarFileName(),
                'lastMessageContent' => $user->getLastMessageContent()
            ];
        }, $users);
    }
}