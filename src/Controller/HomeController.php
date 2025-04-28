<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    #[Route('/add', name: 'app_home_page')]
    public function index1(): Response
    {
        // Création d'un nouvel objet Produit
        $produit = new Produit();
        
        // Création du formulaire en associant l'objet Produit
        $form = $this->createForm(ProduitType::class, $produit);
        
        // Vérification si le formulaire a été soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Vous pouvez ajouter une logique pour traiter le formulaire, 
            // comme persister l'entité ou rediriger après soumission
        }

        // Renvoi de la vue avec le formulaire
        return $this->render('base.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
