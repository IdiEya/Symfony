<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CourRepository;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\UniqueSallePerCreneau;



#[ORM\Entity(repositoryClass: CourRepository::class)]
#[ORM\Table(name: 'cours')]
class Cour
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

    // Changed to 'datetime' to store both date and time
    #[ORM\Column(name: 'date_debut', type: 'date', nullable: false)]
    private ?\DateTimeInterface $dateDebut = null;
    
    
    

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    // Changed to 'datetime' to store both date and time
    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?\DateTimeInterface $dateFin = null;
    

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $localisation = null;

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(string $localisation): self
    {
        $this->localisation = $localisation;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    // Many-to-One relation with 'Salle'
    #[Assert\NotNull(message: 'Veuillez choisir une salle.')]
    #[ORM\ManyToOne(targetEntity: Salle::class, inversedBy: 'cours')]
    #[ORM\JoinColumn(nullable: true)]
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

    // Changed the type to float to match the column type
    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: false)]
    private ?float $prix = null;

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $placesDisponibles = null;

    public function getPlacesDisponibles(): ?int
    {
        return $this->placesDisponibles;
    }

    public function setPlacesDisponibles(?int $placesDisponibles): self
    {
        $this->placesDisponibles = $placesDisponibles;
        return $this;
    }
}
