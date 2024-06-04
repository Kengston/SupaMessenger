<?php

namespace App\Controller;

use App\Form\MessageType;
use App\Form\UserType;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use App\Service\DialogService;
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
    public function __construct(
        private UserRepository $userRepository,
        private MessageService $messageService,
        private UserService $userService,
        private DialogService $dialogService
    ) {}

    #[Route('/user/dialog/{id}', name: 'app_dialog')]
    public function dialog(int $id = null, Request $request, PublisherInterface $publisher): Response
    {
        $currentUser = $this->getUser();

        $avatarForm = $this->createForm(UserType::class, $currentUser);

        $selectedUser = $id ? $this->userRepository->find($id) : null;
        $selectedUserChangeStatusAt = $selectedUser?->getChangeStatusAt();

        $messages = $selectedUser ? $this->dialogService->getFormattedDialogMessages($currentUser, $selectedUser) : [];

        $avatarForm->handleRequest($request);

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


        if ($selectedUser) {
            $this->dialogService->markDialogAsRead($currentUser, $selectedUser);
        }

        $users = $this->userRepository->findAll();

        $lastMessagesInDialogArray = $this->dialogService->getLastMessagesInDialog($currentUser, $users);
        $unreadMessageStatusArray = $this->messageService->getUnreadMessagesStatusForUsers($currentUser, $users);
        $usersJson = json_encode($this->userService->getUsersListSerialized($users));

        return $this->render('messages/dialog.html.twig', [
            'lastMessagesInDialogArray' => $lastMessagesInDialogArray,
            'unreadMessageStatusArray' => $unreadMessageStatusArray,
            'selectedUser' => $selectedUser,
            'selectedUserChangeStatusAt' => $selectedUserChangeStatusAt,
            'currentUser' => $currentUser,
            'messages' => $messages,
            'users' => $usersJson,
            'avatarForm' => $avatarForm->createView(),
            'id' => $id
        ]);
    }

    #[Route('/user/dialog/{id}/delete', name: 'app_delete_dialog')]
    public function deleteDialog(int $id, PublisherInterface $publisher, DialogService $dialogService, MessageRepository $messageRepository)
    {
        $currentUser = $this->getUser();
        $selectedUser = $this->userRepository->find($id);
        $selectedDialog = $messageRepository->findDialogMessages($currentUser, $selectedUser);



        if (!$selectedDialog) {
            throw $this->createNotFoundException('Dialog not found');
        }

        $dialogService->deleteAllMessagesInDialog($selectedDialog);

        $update = new Update(
            '/dialog/user/'.$selectedUser->getId(),
            json_encode(['delete' => 'dialog'])
        );
        $publisher($update);

        return $this->redirectToRoute('app_dialog', ['id' => $selectedUser->getId()]);
    }

    #[Route('/user/dialog/{id}/updates', name: 'fetch_dialog_updates')]
    public function fetchUpdates(int $id): JsonResponse
    {
        $currentUser = $this->getUser();
        $selectedUser = $this->userRepository->find($id);
        $formattedMessages = $this->dialogService->getFormattedDialogMessages($currentUser, $selectedUser);

        return $this->json($formattedMessages);
    }
}
