<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Csrf\CsrfToken;
    use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
class UserController extends AbstractController
{
    // Route pour afficher le tableau de bord
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function dashboard(EntityManagerInterface $em)
    {
        // Récupérer tous les utilisateurs
        $users = $em->getRepository(User::class)->findAll();
        $user = $this->getUser();
        // Rendre la vue et passer la variable 'users' à Twig
        return $this->render('admin/dashboard.html.twig', [
            'users' => $users,  // Passer la liste des utilisateurs à Twig
            'user' => $user,
        ]);
    }

    // Route pour éditer un utilisateur
    
    
    #[Route('/admin/users/edit', name: 'admin_user_edit', methods: ['POST'])]
    public function editUser(Request $request, EntityManagerInterface $em, CsrfTokenManagerInterface $csrfTokenManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
    
        // Validate CSRF token
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('user_edit', $data['_token'] ?? ''))) {
            return new JsonResponse(['error' => 'Invalid CSRF token.'], 401);
        }
    
        $user = $em->getRepository(User::class)->find($data['id']);
    
        if (!$user) {
            return new JsonResponse(['error' => 'User not found.'], 404);
        }
    
        try {
            $user->setRole($data['role']);
            $user->setNote($data['note']);
            $user->setPrenom($data['prenom']);
            $user->setNom($data['nom']);
            $user->setEmail($data['email']);
            $user->setTelephone($data['telephone']);
            $user->setAdresse($data['adresse']);
            $user->setSpecialite($data['specialite'] ?? null);
    
            $em->flush();
            return new JsonResponse(['message' => 'User updated successfully.']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Error updating user.'], 500);
        }
    }}