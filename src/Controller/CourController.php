<?php

namespace App\Controller;

use App\Entity\Cour;
use App\Form\CourType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormError;

class CourController extends AbstractController
{
    // Route pour créer un cours
    #[Route('/cour/new', name: 'cour_new')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
{
    $cour = new Cour();  // Création d'un nouvel objet Cour
    $form = $this->createForm(CourType::class, $cour);
    $form->handleRequest($request);

    if ($form->isSubmitted()) {

        $localisation = $form->get('localisation')->getData();
        $description = $form->get('description')->getData();
        $prix = $form->get('prix')->getData();
        $places = $form->get('placesDisponibles')->getData();
        $salle = $form->get('salle')->getData();
    
        $hasError = false;
    
        // Localisation ne doit pas être un nombre
        if (is_numeric($localisation)) {
            $form->get('localisation')->addError(new FormError('La localisation ne doit pas être un nombre.'));
            $hasError = true;
        }
    
        // Description ne doit pas être un nombre
        if (is_numeric($description)) {
            $form->get('description')->addError(new FormError('La description ne doit pas être un nombre.'));
            $hasError = true;
        }
    
        // Prix doit être un nombre
        if (!is_numeric($prix)) {
            $form->get('prix')->addError(new FormError('Le prix doit être un nombre.'));
            $hasError = true;
        }
    
        // Places doit être un entier
        if (!is_numeric($places)) {
            $form->get('placesDisponibles')->addError(new FormError('Le nombre de places doit être un nombre.'));
            $hasError = true;
        } elseif ($places > 20) {
            $form->get('placesDisponibles')->addError(new FormError('Le nombre de places ne peut pas être supérieur à 20.'));
            $hasError = true;
        }
    
        // Salle obligatoire
        if (!$salle) {
            $form->get('salle')->addError(new FormError('La salle est obligatoire.'));
            $hasError = true;
        }
    
        // S'il y a une erreur → ne pas continuer
        if ($hasError) {
            return $this->render('cour/create.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    
        // Vérification doublon
        $existingCourse = $entityManager->getRepository(Cour::class)->findOneBy([
            'dateDebut' => $cour->getDateDebut(),
            'dateFin' => $cour->getDateFin(),
            'salle' => $cour->getSalle(),
            'prix' => $cour->getPrix(),
            'localisation' => $cour->getLocalisation(),
            'placesDisponibles' => $cour->getPlacesDisponibles()
        ]);
    
        if ($existingCourse) {
            $form->get('dateDebut')->addError(new FormError('Plage horaire déjà réservée.'));
            $form->get('dateFin')->addError(new FormError('Plage horaire déjà réservée.'));
            $form->get('localisation')->addError(new FormError('Localisation déjà utilisée.'));
            $form->get('description')->addError(new FormError('Description déjà attribuée.'));
            $form->get('prix')->addError(new FormError('Prix déjà utilisé.'));
            $form->get('placesDisponibles')->addError(new FormError('Places déjà réservées.'));
            
            return $this->render('cour/create.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    
        // Enregistrement
        $entityManager->persist($cour);
        $entityManager->flush();
    
        $this->addFlash('success', 'Le cours a été ajouté avec succès.');
        return $this->redirectToRoute('cour_list');
    }
    

    return $this->render('cour/create.html.twig', [
        'form' => $form->createView(),
    ]);
}

    // Route pour lister les cours
    #[Route('/cour', name: 'cour_list')]
    public function list(Request $request, EntityManagerInterface $entityManager): Response
    {
        $search = $request->query->get('search');
    
        $qb = $entityManager->getRepository(Cour::class)->createQueryBuilder('c')
            ->leftJoin('c.salle', 's')
            ->addSelect('s');
    
        if ($search) {
            $qb->where(
                $qb->expr()->orX(
                    $qb->expr()->like('LOWER(c.localisation)', ':search'),
                    $qb->expr()->like('LOWER(c.description)', ':search'),
                    $qb->expr()->like('LOWER(s.nom)', ':search')
                )
            )
            ->setParameter('search', '%' . strtolower($search) . '%');
        }
    
        $cours = $qb->getQuery()->getResult();
    
        return $this->render('cour/list.html.twig', [
            'cours' => $cours,
        ]);
    }
    
    
    
    // Route pour éditer un cours
    #[Route('/cour/edit/{id}', name: 'cour_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, Cour $cour): Response
    {
        $form = $this->createForm(CourType::class, $cour);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $localisation = $form->get('localisation')->getData();
            $description = $form->get('description')->getData();
            $prix = $form->get('prix')->getData();
            $places = $form->get('placesDisponibles')->getData();
            $salle = $form->get('salle')->getData();
        
            $hasError = false;
        
            // Localisation ne doit pas être un nombre
            if (is_numeric($localisation)) {
                $form->get('localisation')->addError(new FormError('La localisation ne doit pas être un nombre.'));
                $hasError = true;
            }
        
            // Description ne doit pas être un nombre
            if (is_numeric($description)) {
                $form->get('description')->addError(new FormError('La description ne doit pas être un nombre.'));
                $hasError = true;
            }
        
            // Prix doit être un nombre
            if (!is_numeric($prix)) {
                $form->get('prix')->addError(new FormError('Le prix doit être un nombre.'));
                $hasError = true;
            }
        
            // Places doit être un entier
            if (!is_numeric($places)) {
                $form->get('placesDisponibles')->addError(new FormError('Le nombre de places doit être un nombre.'));
                $hasError = true;
            } elseif ($places > 20) {
                $form->get('placesDisponibles')->addError(new FormError('Le nombre de places ne peut pas être supérieur à 20.'));
                $hasError = true;
            }
        
            // Salle obligatoire
            if (!$salle) {
                $form->get('salle')->addError(new FormError('La salle est obligatoire.'));
                $hasError = true;
            }
        
            // S'il y a une erreur → ne pas continuer
            if ($hasError) {
                return $this->render('cour/create.html.twig', [
                    'form' => $form->createView(),
                ]);
            }
        
            // Vérification doublon
            $existingCourse = $entityManager->getRepository(Cour::class)->findOneBy([
                'dateDebut' => $cour->getDateDebut(),
                'dateFin' => $cour->getDateFin(),
                'salle' => $cour->getSalle(),
                'prix' => $cour->getPrix(),
                'localisation' => $cour->getLocalisation(),
                'placesDisponibles' => $cour->getPlacesDisponibles()
            ]);
        
            if ($existingCourse) {
                $form->get('dateDebut')->addError(new FormError('Plage horaire déjà réservée.'));
                $form->get('dateFin')->addError(new FormError('Plage horaire déjà réservée.'));
                $form->get('localisation')->addError(new FormError('Localisation déjà utilisée.'));
                $form->get('description')->addError(new FormError('Description déjà attribuée.'));
                $form->get('prix')->addError(new FormError('Prix déjà utilisé.'));
                $form->get('placesDisponibles')->addError(new FormError('Places déjà réservées.'));
                
                return $this->render('cour/create.html.twig', [
                    'form' => $form->createView(),
                ]);
            }
        
            // Enregistrement
            $entityManager->persist($cour);
            $entityManager->flush();
        
            $this->addFlash('success', 'Le cours a été ajouté avec succès.');
            return $this->redirectToRoute('cour_list');
        }
        

        return $this->render('cour/edit.html.twig', [
            'form' => $form->createView(),
            'cour' => $cour,
        ]);
    }

    // Route pour supprimer un cours
    #[Route('/cour/delete/{id}', name: 'cour_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, Cour $cour): Response
    {
        if ($this->isCsrfTokenValid('delete' . $cour->getId(), $request->request->get('_token'))) {
            $entityManager->remove($cour);
            $entityManager->flush();

            // Message de succès
            $this->addFlash('success', 'Le cours a été supprimé avec succès.');
        }

        return $this->redirectToRoute('cour_list');
    }
}
