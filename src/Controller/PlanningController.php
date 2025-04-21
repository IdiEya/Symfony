<?php
// src/Controller/PlanningController.php

namespace App\Controller;

use App\Entity\Salle;
use App\Entity\Cour;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlanningController extends AbstractController
{
    private $entityManager;

    // Injection du service Doctrine dans le contrôleur via le constructeur
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/planning', name: 'planning_index')]
    public function index(): Response
    {
        // Récupérer toutes les salles via l'EntityManager
        $salles = $this->entityManager->getRepository(Salle::class)->findAll();
        
        // Exemple d'horaires et de jours
        $horaires = ['8h-10h', '10h-12h', '12h-14h', '14h-16h', '16h-18h'];
        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];

        // Créer un tableau de planning (juste un exemple)
        $planning = [];
        foreach ($jours as $jour) {
            foreach ($salles as $salle) {
                foreach ($horaires as $horaire) {
                    // Vous pouvez récupérer les cours associés à cet horaire, jour, et salle
                    $planning[$jour][$salle->getId()][$horaire] = "Cours de Yoga"; // Exemple de cours
                }
            }
        }

        return $this->render('planning/index.html.twig', [
            'salles' => $salles,
            'horaires' => $horaires,
            'jours' => $jours,
            'planning' => $planning,
        ]);
    }
}
