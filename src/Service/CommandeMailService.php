<?php
namespace App\Service;

use App\Entity\Commande;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Twig\Environment;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh; // Corrigé ici
use Endroid\QrCode\Writer\PngWriter;

class CommandeMailService
{
    private $mailer;
    private $twig;
    private $kernel;

    public function __construct(MailerInterface $mailer, Environment $twig, KernelInterface $kernel)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->kernel = $kernel;
    }

    public function sendCommandeEmail(Commande $commande): void
{
    // Concaténer les détails de la commande dans une chaîne
    $commandeDetails = 'Commande ID: ' . $commande->getId() . "\n" .
        'Produit: ' . $commande->getNomProduit() . "\n" .
        'Date: ' . $commande->getDate()->format('Y-m-d H:i:s') . "\n" .
        'Localisation: ' . $commande->getLocalisation() . "\n" .
        'Téléphone: ' . $commande->getTelephone() . "\n" .
        'Email: ' . $commande->getMail() . "\n" .
        'Nombre: ' . $commande->getNombre() . "\n" .
        'Prix: ' . $commande->getPrix() . "\n" .
        'Total: ' . $commande->getTotal();

    // Génération du QR Code avec toutes les informations
    $qrResult = Builder::create()
        ->writer(new PngWriter())
        ->data($commandeDetails) // Utilisation des détails de la commande
        ->encoding(new Encoding('UTF-8'))
        ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())  // Utilisation de ErrorCorrectionLevelHigh
        ->size(300)
        ->margin(10)
        ->build();

    // Convertir le QR Code en Base64
    $qrCodeBase64 = base64_encode($qrResult->getString());

    // Enregistrer le QR Code temporairement
    $qrPath = $this->kernel->getProjectDir() . '/public/uploads/qrcode_' . $commande->getId() . '.png';
    $qrResult->saveToFile($qrPath); // Sauvegarder le fichier QR Code temporaire

    // Générer le HTML avec le QR code en Base64
    $html = $this->twig->render('commande/pdf.html.twig', [
        'commande' => $commande,
        'qrCodeBase64' => $qrCodeBase64, // Passer la variable qrCodeBase64 à Twig
    ]);

    // Génération du PDF
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $pdfContent = $dompdf->output();

    $pdfPath = $this->kernel->getProjectDir() . '/public/uploads/commande_' . $commande->getId() . '.pdf';
    file_put_contents($pdfPath, $pdfContent);

    // Créer l'e-mail
    $email = (new Email())
        ->from('idieya504@gmail.com')
        ->to($commande->getMail())
        ->subject('Confirmation de votre commande')
        ->text('Merci pour votre commande. Vous trouverez votre QR code et votre facture en pièce jointe.')
        ->attachFromPath($pdfPath, 'commande_' . $commande->getId() . '.pdf', 'application/pdf')
        ->attachFromPath($qrPath, 'qrcode_commande_' . $commande->getId() . '.png', 'image/png');

    $this->mailer->send($email);

    // Nettoyer les fichiers temporaires
    unlink($pdfPath);
    unlink($qrPath);
}
}