<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\GymRepository;

#[ORM\Entity(repositoryClass: GymRepository::class)]
#[ORM\Table(name: 'gym')]
class Gym
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

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $photo = null;

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $horaires = null;

    public function getHoraires(): ?string
    {
        return $this->horaires;
    }

    public function setHoraires(?string $horaires): self
    {
        $this->horaires = $horaires;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $contact = null;

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(?string $contact): self
    {
        $this->contact = $contact;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $service = null;

    public function getService(): ?string
    {
        return $this->service;
    }

    public function setService(?string $service): self
    {
        $this->service = $service;
        return $this;
    }

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'gym')]
    #[ORM\JoinColumn(name: 'responsable_id', referencedColumnName: 'id', unique: true)]
    private ?User $user = null;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Abonnement::class, mappedBy: 'gym')]
    private Collection $abonnements;

    /**
     * @return Collection<int, Abonnement>
     */
    public function getAbonnements(): Collection
    {
        if (!$this->abonnements instanceof Collection) {
            $this->abonnements = new ArrayCollection();
        }
        return $this->abonnements;
    }

    public function addAbonnement(Abonnement $abonnement): self
    {
        if (!$this->getAbonnements()->contains($abonnement)) {
            $this->getAbonnements()->add($abonnement);
        }
        return $this;
    }

    public function removeAbonnement(Abonnement $abonnement): self
    {
        $this->getAbonnements()->removeElement($abonnement);
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Salle::class, mappedBy: 'gym')]
    private Collection $salles;

    public function __construct()
    {
        $this->abonnements = new ArrayCollection();
        $this->salles = new ArrayCollection();
    }

    /**
     * @return Collection<int, Salle>
     */
    public function getSalles(): Collection
    {
        if (!$this->salles instanceof Collection) {
            $this->salles = new ArrayCollection();
        }
        return $this->salles;
    }

    public function addSalle(Salle $salle): self
    {
        if (!$this->getSalles()->contains($salle)) {
            $this->getSalles()->add($salle);
        }
        return $this;
    }

    public function removeSalle(Salle $salle): self
    {
        $this->getSalles()->removeElement($salle);
        return $this;
    }

}
