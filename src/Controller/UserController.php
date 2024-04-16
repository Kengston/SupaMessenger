<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $em
    ) {}

    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        $userName = $this->getUser() ? $this->getUser()->getUserIdentifier() : null;

        return $this->render('user/user.html.twig', [
            'userName' => $userName,
        ]);
    }

    #[Route('user/status/{id}', name: 'app_user_status')]
    public function userStatus(int $id, PublisherInterface $publisher): Response
    {
        $selectedUser = $this->userRepository->find($id);
        if (!$selectedUser) {
            throw $this->createNotFoundException('User not found');
        }

        $update = new Update(
            '/dialog/user/' . $selectedUser->getId(),
            json_encode(['status' => $selectedUser->getStatus()])
        );
        $publisher($update);

        return $this->json(['message' => 'User status updated']);
    }
}
