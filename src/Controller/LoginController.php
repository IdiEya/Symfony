<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LoginController extends AbstractController
{
    private $authUtils;
    private $mailer;
    private $entityManager;
    private $passwordHasher;

    public function __construct(
        AuthenticationUtils $authUtils,
        MailerInterface $mailer,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->authUtils = $authUtils;
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/', name: 'app_login')]
    public function login(Request $request): Response
    {
        $error = $this->authUtils->getLastAuthenticationError();
        $lastUsername = $this->authUtils->getLastUsername();

        return $this->render('login/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function forgotPassword(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');

            $user = $this->getUserByEmail($email);

            if ($user) {
                $token = bin2hex(random_bytes(16));
                $user->setPasswordResetToken($token);
                $user->setPasswordResetRequestedAt(new \DateTime());
                $this->entityManager->flush();

                $this->sendPasswordResetEmail($user, $token);

                $this->addFlash('success', 'Un email de réinitialisation a été envoyé à votre adresse.');
            } else {
                $this->addFlash('error', 'Cet email n\'est pas associé à un compte.');
            }

            return $this->redirectToRoute('app_forgot_password');
        }

        return $this->render('login/forgot_password.html.twig');
    }

    #[Route('/reset-password/{token}', name: 'app_reset_password')]
    public function resetPassword($token, Request $request): Response
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['passwordResetToken' => $token]);

        if (!$user || $user->getPasswordResetRequestedAt() < new \DateTime('-1 hour')) {
            $this->addFlash('error', 'Le lien est invalide ou a expiré.');
            return $this->redirectToRoute('app_login');
        }

        if ($request->isMethod('POST')) {
            $newPassword = $request->request->get('password');
            $user->setPassword($this->passwordHasher->hashPassword($user, $newPassword));
            $user->setPasswordResetToken(null);
            $user->setPasswordResetRequestedAt(null);
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('login/reset_password.html.twig', [
            'token' => $token
        ]);
    }

    #[Route("/logout", name: "app_logout")]
    public function logout(): void
    {
        // Symfony gère automatiquement la déconnexion via le firewall
    }

    private function sendPasswordResetEmail(User $user, string $token): void
    {
        $resetLink = $this->generateUrl('app_reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

        $emailMessage = (new Email())
            ->from('idieya504@gmail.com')
            ->to($user->getEmail())
            ->subject('Réinitialisation de votre mot de passe')
            ->html('<p>Nous avons reçu une demande de réinitialisation de mot de passe. Cliquez sur le lien ci-dessous pour réinitialiser votre mot de passe :</p><a href="' . $resetLink . '">Réinitialiser mon mot de passe</a>');

        $this->mailer->send($emailMessage);
    }

    private function getUserByEmail(string $email)
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
    }
}
