<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{

    private $userRepository;
    private $em;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $em) {
        $this->userRepository = $userRepository;
        $this->em = $em;
    }
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        $userName = $this->getUser() ? $this->getUser()->getUserIdentifier() : null;

        return $this->render('user/user.html.twig', [
            'userName' => $userName,
        ]);
    }

    #[Route('user/select', name: 'app_user_select')]
    public function selectUser() {
        $users = $this->userRepository->findAll();

        return $this->render('user/select_user.html.twig', [
            'users' => $users,
        ]);
    }
}
