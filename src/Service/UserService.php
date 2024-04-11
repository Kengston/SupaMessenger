<?php

// src/Service/FileUploader.php
namespace App\Service;

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
            // You might want to re-throw the exception here or return false
            throw new \Exception('Unable to upload the avatar: '.$e->getMessage());
        }

        // Persist the avatar path to user entity

        $user->setAvatarFileName($avatarPath);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}