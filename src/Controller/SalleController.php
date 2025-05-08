<?php
namespace App\Controller;

use App\Entity\Salle;
use App\Form\SalleType;
use App\Repository\SalleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SalleController extends AbstractController
{
    private $entityManager;

    // Injecting EntityManagerInterface
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/salle', name: 'salle_index')]
    public function index(Request $request, SalleRepository $salleRepository): Response
    {
        $search = $request->query->get('search');
        $user = $this->getUser();
    
        if ($search) {
            $salles = $salleRepository->createQueryBuilder('s')
                ->where('LOWER(s.nom) LIKE :search')
                ->orWhere('LOWER(s.specialite) LIKE :search')
                ->orWhere('LOWER(s.description) LIKE :search')
                ->setParameter('search', '%' . strtolower($search) . '%')
                ->getQuery()
                ->getResult();
        } else {
            $salles = $salleRepository->findAll();
        }
    
        return $this->render('salle/index.html.twig', [
            'salles' => $salles,
            'user' => $user,
        ]);
    }
    
    
#[Route('/salle/ajouter', name: 'salle_add')]
public function add(Request $request, SalleRepository $salleRepository): Response
{
    $salle = new Salle();
    $form = $this->createForm(SalleType::class, $salle);
    $form->handleRequest($request);
    $user = $this->getUser();

    if ($form->isSubmitted()) {
        $numero = $form->get('numero')->getData();
        $specialite = $form->get('specialite')->getData();
        $capacite = $form->get('capacite')->getData();
        $description = $form->get('description')->getData();
        $nom = $form->get('nom')->getData();

        $errors = [];

        if (empty($numero)) {
            $errors['numero'] = 'Veuillez remplir le numéro de la salle.';
        } elseif (!is_numeric($numero)) {
            $errors['numero'] = 'Le numéro doit être un nombre.';
        }

        if (empty($specialite)) {
            $errors['specialite'] = 'Veuillez préciser la spécialité.';
        } elseif (is_numeric($specialite)) {
            $errors['specialite'] = 'La spécialité ne peut pas être un nombre.';
        }

        if (empty($description)) {
            $errors['description'] = 'Veuillez remplir la description.';
        } elseif (is_numeric($description)) {
            $errors['description'] = 'La description ne peut pas être un nombre.';
        }

        if (empty($nom)) {
            $errors['nom'] = 'Veuillez remplir le nom de la salle.';
        } elseif (is_numeric($nom)) {
            $errors['nom'] = 'Le nom de la salle ne peut pas être un numéro uniquement.';
        } elseif ($salleRepository->findOneBy(['nom' => $nom])) {
            $errors['nom'] = 'Ce nom de salle existe déjà. Veuillez en choisir un autre.';
        }

        if (empty($capacite)) {
            $errors['capacite'] = 'Veuillez remplir la capacité de la salle.';
        } elseif ($capacite > 20) {
            $errors['capacite'] = 'La capacité maximale autorisée est de 20.';
        }

        foreach ($errors as $field => $message) {
            $form->get($field)->addError(new \Symfony\Component\Form\FormError($message));
        }

        if (empty($errors)) {
            $this->entityManager->persist($salle);
            $this->entityManager->flush();
            $this->addFlash('success', 'Salle ajoutée avec succès !');
            return $this->redirectToRoute('salle_index');
        }
    }

    return $this->render('salle/add.html.twig', [
        'form' => $form->createView(),
        'user' => $user,
    ]);
}




    #[Route('/salle/{id}/modifier', name: 'salle_edit')]
    public function edit(Request $request, Salle $salle, SalleRepository $salleRepository): Response
    {
        $form = $this->createForm(SalleType::class, $salle);
        $form->handleRequest($request);
        $user = $this->getUser();
    
        if ($form->isSubmitted()) {
            $numero = $form->get('numero')->getData();
            $specialite = $form->get('specialite')->getData();
            $capacite = $form->get('capacite')->getData();
            $description = $form->get('description')->getData();
            $nom = $form->get('nom')->getData();
    
            $errors = [];
    
            if (empty($numero)) {
                $errors['numero'] = 'Numéro requis.';
            } elseif (!is_numeric($numero)) {
                $errors['numero'] = 'Le numéro doit être numérique.';
            }
    
            if (empty($specialite)) {
                $errors['specialite'] = 'Veuillez préciser la spécialité.';
            } elseif (is_numeric($specialite)) {
                $errors['specialite'] = 'La spécialité ne peut pas être un nombre';
            }
    
            if (empty($capacite)) {
                $errors['capacite'] = 'Veuillez remplir la capacité de la salle.';
            } elseif ($capacite > 20) {
                $errors['capacite'] = 'La capacité maximale autorisée est de 20.';
            }
    
            if (empty($description)) {
                $errors['description'] = 'Veuillez remplir la description.';
            } elseif (is_numeric($description)) {
                $errors['description'] = 'La description ne peut pas être un nombre.';
            }
    
            if (empty($nom)) {
                $errors['nom'] = 'Veuillez remplir le nom de la salle.';
            } elseif (is_numeric($nom)) {
                $errors['nom'] = 'Le nom de la salle ne peut pas être un numéro uniquement.';
            } 
    
            foreach ($errors as $field => $message) {
                $form->get($field)->addError(new \Symfony\Component\Form\FormError($message));
            }
    
            if (empty($errors)) {
                $this->entityManager->flush();
                $this->addFlash('success', 'Salle modifiée avec succès !');
                return $this->redirectToRoute('salle_index');
            }
        }
    
        return $this->render('salle/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
    
    #[Route('/salle/delete/{id}', name: 'salle_delete', methods: ['POST'])]
    public function delete(Request $request, Salle $salle): Response
    {
        if ($this->isCsrfTokenValid('delete' . $salle->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($salle);
            $this->entityManager->flush();

            $this->addFlash('success', 'La salle a été supprimée avec succès.');
        }

        return $this->redirectToRoute('salle_index');
    }

}

