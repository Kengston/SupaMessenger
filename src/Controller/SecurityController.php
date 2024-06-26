<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Check if the user is already authenticated
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dialog', ['id' => 0]);
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(EntityManagerInterface $entityManager, Security $security): void
    {
        $user = $security->getUser();

        if ($user instanceof User) {
            // Update user status to "offline"
            $user->setStatus('offline');
            $entityManager->persist($user);
            $entityManager->flush();
        }

        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
