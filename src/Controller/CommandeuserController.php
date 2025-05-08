<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;



#[Route('/commandeuser')]
final class CommandeuserController extends AbstractController
{
    #[Route('/', name: 'app_commandeuser_index', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository): Response
    {
        return $this->render('commandeuser/index.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }

    #[Route('/produit/{produitId}/new', name: 'app_commandeuser_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        ProduitRepository $produitRepository,
        int $produitId
    ): Response {
        $produit = $produitRepository->find($produitId);

        if (!$produit) {
            throw $this->createNotFoundException('Produit non trouvé');
        }

        $commande = new Commande();
        $commande->setDate(new \DateTime());

        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $quantiteCommandee = $commande->getNombre();
            $ancienneQuantite = $produit->getQuantite();

            if ($quantiteCommandee > $ancienneQuantite) {
                $this->addFlash('error', 'Quantité insuffisante en stock !');
                return $this->redirectToRoute('app_produituser_index');
            }

            // Mise à jour de la quantité du produit
            $produit->setQuantite($ancienneQuantite - $quantiteCommandee);

            // Mise à jour de la commande
            $commande
                ->setTotal($produit->getPrix() * $quantiteCommandee)
                ->setPrix($produit->getPrix())
                ->setNomProduit($produit->getNom());

            $commande->setProduit($produit); // si relation ManyToOne

            $entityManager->persist($commande);
            $entityManager->flush();

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => true]);
            }

            return $this->redirectToRoute('commande_success');
        }

        $response = $this->render('commandeuser/new.html.twig', [
            'form' => $form->createView(),
              'produit' => $produit, // <-- Ajout ici
        ]);

        if ($request->isXmlHttpRequest()) {
            $response->headers->set('Content-Type', 'text/html');
        }

        return $response;
    }

    #[Route('/{id}', name: 'commandeuser_modal_show', methods: ['GET'])]
    public function showModal(Commande $commande): Response
    {
        return $this->render('commandeuser/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/success', name: 'commande_success', methods: ['GET'])]
    public function success(): Response
    {
        return $this->redirectToRoute('app_commandeuser_index');
    }

    #[Route('/{id}/edit', name: 'app_commande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_commandeuser_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $commande->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commandeuser_index', [], Response::HTTP_SEE_OTHER);
    }

 
        #[Route('/pdf/{id}', name: 'commandeuser_pdf', methods: ['GET'])]
        public function generatePdf($id, CommandeRepository $commandeRepository): Response
        {
            $commande = $commandeRepository->find($id);
    
            if (!$commande) {
                throw $this->createNotFoundException('Commande non trouvée');
            }
    
            // Création du QR Code avec toutes les informations de la commande
            $qrCodeData = "ID: " . $commande->getId() . "\n"
                        . "Produit: " . $commande->getNomProduit() . "\n"
                        . "Quantité: " . $commande->getNombre() . "\n"
                        . "Prix Unitaire: " . $commande->getPrix() . "\n"
                        . "Total: " . $commande->getTotal() . "\n"
                        . "Date: " . $commande->getDate()->format('Y-m-d H:i:s') . "\n"
                        . "Localisation: " . $commande->getLocalisation() . "\n"
                        . "Téléphone: " . $commande->getTelephone() . "\n"
                        . "Email: " . $commande->getMail();
    
            $qrCode = new QrCode($qrCodeData);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            $qrCodeBase64 = base64_encode($result->getString()); // QR code en base64
    
            // Générer le HTML pour le PDF
            $html = $this->renderView('commande/pdf.html.twig', [
                'commande' => $commande,
                'qrCodeBase64' => $qrCodeBase64, // Passer la variable qrCodeBase64 à la vue
            ]);
    
            // Initialisation de Dompdf pour générer le PDF
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);
    
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
    
            return new Response(
                $dompdf->output(),
                200,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="commande_' . $id . '.pdf"',
                ]
            );
        }
        #[Route('/export/pdf', name: 'commande_export_pdf1')]
public function exportPdf(CommandeRepository $commandeRepository): Response
{
    $commandes = $commandeRepository->findAll();

    $dompdf = new Dompdf();
    $options = new Options();
    $options->set('defaultFont', 'Arial');
    $dompdf->setOptions($options);

    $html = $this->renderView('commande/pdf.html.twig', ['commandes' => $commandes]);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();

    return new Response(
        $dompdf->output(),
        200,
        ['Content-Type' => 'application/pdf',
         'Content-Disposition' => 'attachment; filename="commandes.pdf"']
    );
}


#[Route('/export/excel', name: 'commande_export_excel1')]
public function exportExcel(CommandeRepository $commandeRepository): Response
{
    $commandes = $commandeRepository->findAll();

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->fromArray(['Date', 'Localisation', 'Téléphone', 'Mail', 'Nombre', 'Prix', 'Total', 'Nom Produit'], null, 'A1');

    $row = 2;
    foreach ($commandes as $commande) {
        $sheet->fromArray([
            $commande->getDate()?->format('Y-m-d H:i:s'),
            $commande->getLocalisation(),
            $commande->getTelephone(),
            $commande->getMail(),
            $commande->getNombre(),
            $commande->getPrix(),
            $commande->getTotal(),
            $commande->getNomProduit(),
        ], null, 'A' . $row++);
    }

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $fileName = 'commandes.xlsx';
    $temp_file = tempnam(sys_get_temp_dir(), $fileName);
    $writer->save($temp_file);

    return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_ATTACHMENT);
}
#[Route('/export/word', name: 'commande_export_word1')]
public function exportWord(CommandeRepository $commandeRepository): Response
{
    $commandes = $commandeRepository->findAll();
    $html = $this->renderView('commande/word.html.twig', ['commandes' => $commandes]);

    return new Response(
        $html,
        200,
        [
            'Content-Type' => 'application/msword',
            'Content-Disposition' => 'attachment; filename="commandes.doc"',
        ]
    );
}

    }
    