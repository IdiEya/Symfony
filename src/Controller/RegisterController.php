<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        if ($request->isMethod('POST')) {
            $firstName = $request->request->get('prenom');
            $lastName = $request->request->get('nom');
            $email = $request->request->get('email');
            $phone = $request->request->get('telephone');
            $address = $request->request->get('adresse');
            $speciality = $request->request->get('specialite');
            $password = $request->request->get('password');
            $confirmPassword = $request->request->get('confirm_password');

            if ($password !== $confirmPassword) {
                $this->addFlash('error', 'Passwords do not match.');
                return $this->redirectToRoute('app_register');
            }

            $user = new User();
            $user->setPrenom($firstName);
            $user->setNom($lastName);
            $user->setEmail($email);
            $user->setTelephone($phone);
            $user->setAdresse($address);
            $user->setSpecialite($speciality);

            // Handle photo upload
            $photoFile = $request->files->get('photo');
            if ($photoFile) {
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();

                try {
                    $photoFile->move(
                        $this->getParameter('profile_photos_directory'),
                        $newFilename
                    );
                    $user->setPhoto($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Failed to upload profile photo.');
                    return $this->redirectToRoute('app_register');
                }
            }

            $hashedPassword = $passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Account created successfully. You can now log in.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('signup/signup.html.twig');
    }
}
