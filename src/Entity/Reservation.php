<?php
// src/Entity/Reservation.php
namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    private ?string $nom = null;

    #[ORM\Column(type: 'string')]
    private ?string $prenom = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $date_reservation = null;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\Regex(
        pattern: "/^\+?\d+$/",
        message: "Le numéro de téléphone doit être au format international (ex: +1234567890)"
    )]
    private ?string $tel = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(type: 'float')]
    private $latitude;

    #[ORM\Column(type: 'float')]
    private $longitude;
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $adresseComplete = null;
    // --- Getters et Setters ---
    public function getAdresseComplete(): ?string
    {
        return $this->adresseComplete;
    }
    
    public function setAdresseComplete(?string $adresseComplete): self
    {
        $this->adresseComplete = $adresseComplete;
        return $this;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getDateReservation(): ?\DateTimeInterface
    {
        return $this->date_reservation;
    }

    public function setDateReservation(?\DateTimeInterface $date): self
    {
        $this->date_reservation = $date;
        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {
        $this->tel = $tel;
        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;
        return $this;
    }
    
}
