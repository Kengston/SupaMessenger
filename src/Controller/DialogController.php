<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Form\UserType;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use App\Service\MessageService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Attribute\Route;

class DialogController extends AbstractController
{
    private $userRepository;
    private $messageService;
    private $messageRepository;

    public function __construct(UserRepository $userRepository, MessageService $messageService, MessageRepository $messageRepository, UserService $userService)
    {
        $this->userRepository = $userRepository;
        $this->messageService = $messageService;
        $this->messageRepository = $messageRepository;
        $this->userService = $userService;
    }

    #[Route('/user/dialog/{id}', name: 'app_dialog')]
    public function dialog(int $id = null, Request $request, PublisherInterface $publisher): Response
    {
        $currentUser = $this->getUser();

        $avatarForm = $this->createForm(UserType::class, $currentUser);
        $messageForm = $this->createForm(MessageType::class, new Message());

        $selectedUser = $id ? $this->userRepository->find($id) : null;
        $selectedUserChangeStatusAt = $selectedUser?->getChangeStatusAt();

        $messages = $selectedUser ? $this->messageService->getFormattedDialogMessages($currentUser, $selectedUser) : [];

        $avatarForm->handleRequest($request);
        $messageForm->handleRequest($request);

        if ($avatarForm->isSubmitted() && $avatarForm->isValid()) {
            $avatarFile = $avatarForm->get('avatarFileName')->getData();

            if ($avatarFile) {
                try {
                    $this->userService->uploadAvatar($currentUser, $avatarFile);
                } catch (\Throwable $e) {
                    throw $this->createNotFoundException('Unable to upload the avatar');
                }
            }
        }

        if ($messageForm->isSubmitted() && $messageForm->isValid()) {

            $uploadedFile = $messageForm->get('photoData')->getData();
            $photoFilename = null;

            if ($uploadedFile) {
                try {
                    $photoFilename = $this->messageService->uploadPhoto($uploadedFile);
                } catch (\Throwable $e) {
                    throw $this->createNotFoundException('Unable to upload the photo');
                }
            }

            $newMessage = $this->messageService->createAndPersist($currentUser, $selectedUser, $messageForm->getData()->getContent(), $photoFilename);

            if ($newMessage) {
                $updatedAt = $newMessage->getUpdatedAt() ? $newMessage->getUpdatedAt()->format('H:i') : null;
                $createdAt = $newMessage->getCreatedAt() ? $newMessage->getCreatedAt()->format('H:i') : null;

                $senderUpdate = new Update(
                    '/dialog/user/'.$currentUser->getId(),
                    json_encode([
                        'sender' => $currentUser->getUsername(),
                        'id' => $newMessage->getId(),
                        'content' => $newMessage->getContent(),
                        'updatedAt' => $updatedAt,
                        'createdAt' => $createdAt,
                        'photoData' => $photoFilename
                    ])
                );
                $recipientUpdate = new Update(
                    '/dialog/user/'.$selectedUser->getId(),
                    json_encode([
                        'sender' => $currentUser->getUsername(),
                        'id' => $newMessage->getId(),
                        'content' => $newMessage->getContent(),
                        'updatedAt' => $updatedAt,
                        'createdAt' => $createdAt,
                        'photoData' => $photoFilename
                    ])
                );
                $publisher($senderUpdate);
                $publisher($recipientUpdate);
            }

            return $this->redirectToRoute('app_dialog', ['id' => $selectedUser->getId()]);
        }

        $users = $this->userRepository->findAll();

        return $this->render('messages/dialog.html.twig', [
            'selectedUser' => $selectedUser,
            'selectedUserChangeStatusAt' => $selectedUserChangeStatusAt,
            'currentUser' => $currentUser,
            'messages' => $messages,
            'users' => $users,
            'avatarForm' => $avatarForm->createView(),
            'messageForm' => $messageForm->createView(),
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
}
