<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\ReservationRepository;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ORM\Table(name: 'reservation')]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $nom = null;

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $prenom = null;

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $tel = null;

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {
        $this->tel = $tel;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: false)]
    private ?\DateTimeInterface $date_reservation = null;

    public function getDate_reservation(): ?\DateTimeInterface
    {
        return $this->date_reservation;
    }

    public function setDate_reservation(\DateTimeInterface $date_reservation): self
    {
        $this->date_reservation = $date_reservation;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $heure_reservation = null;

    public function getHeure_reservation(): ?string
    {
        return $this->heure_reservation;
    }

    public function setHeure_reservation(?string $heure_reservation): self
    {
        $this->heure_reservation = $heure_reservation;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Salle::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(name: 'salle_id', referencedColumnName: 'id')]
    private ?Salle $salle = null;

    public function getSalle(): ?Salle
    {
        return $this->salle;
    }

    public function setSalle(?Salle $salle): self
    {
        $this->salle = $salle;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $salle_nom = null;

    public function getSalle_nom(): ?string
    {
        return $this->salle_nom;
    }

    public function setSalle_nom(?string $salle_nom): self
    {
        $this->salle_nom = $salle_nom;
        return $this;
    }

    public function getDateReservation(): ?\DateTimeInterface
    {
        return $this->date_reservation;
    }

    public function setDateReservation(\DateTimeInterface $date_reservation): static
    {
        $this->date_reservation = $date_reservation;

        return $this;
    }

    public function getHeureReservation(): ?string
    {
        return $this->heure_reservation;
    }

    public function setHeureReservation(?string $heure_reservation): static
    {
        $this->heure_reservation = $heure_reservation;

        return $this;
    }

    public function getSalleNom(): ?string
    {
        return $this->salle_nom;
    }

    public function setSalleNom(?string $salle_nom): static
    {
        $this->salle_nom = $salle_nom;

        return $this;
    }

}
