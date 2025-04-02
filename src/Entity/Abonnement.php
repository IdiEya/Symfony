<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\AbonnementRepository;

#[ORM\Entity(repositoryClass: AbonnementRepository::class)]
#[ORM\Table(name: 'abonnement')]
class Abonnement
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
    private ?string $services = null;

    public function getServices(): ?string
    {
        return $this->services;
    }

    public function setServices(string $services): self
    {
        $this->services = $services;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: false)]
    private ?\DateTimeInterface $dateInitiale = null;

    public function getDateInitiale(): ?\DateTimeInterface
    {
        return $this->dateInitiale;
    }

    public function setDateInitiale(\DateTimeInterface $dateInitiale): self
    {
        $this->dateInitiale = $dateInitiale;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: false)]
    private ?\DateTimeInterface $dateExpiration = null;

    public function getDateExpiration(): ?\DateTimeInterface
    {
        return $this->dateExpiration;
    }

    public function setDateExpiration(\DateTimeInterface $dateExpiration): self
    {
        $this->dateExpiration = $dateExpiration;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $type = null;

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    #[ORM\Column(type: 'float', nullable: false)]
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

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $modePaiement = null;

    public function getModePaiement(): ?string
    {
        return $this->modePaiement;
    }

    public function setModePaiement(string $modePaiement): self
    {
        $this->modePaiement = $modePaiement;
        return $this;
    }

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'abonnement')]
    #[ORM\JoinColumn(name: 'sportif_id', referencedColumnName: 'id', unique: true)]
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

    #[ORM\ManyToOne(targetEntity: Gym::class, inversedBy: 'abonnements')]
    #[ORM\JoinColumn(name: 'gym_id', referencedColumnName: 'id')]
    private ?Gym $gym = null;

    public function getGym(): ?Gym
    {
        return $this->gym;
    }

    public function setGym(?Gym $gym): self
    {
        $this->gym = $gym;
        return $this;
    }

}
