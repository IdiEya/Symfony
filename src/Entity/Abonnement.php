<?php

namespace App\Entity;

use App\Enum\TypeAbonnement;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: "App\Repository\AbonnementRepository")]
class Abonnement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', enumType: TypeAbonnement::class)]
    #[Assert\NotBlank(message: "Le type d'abonnement ne peut pas être vide.")]
    private ?TypeAbonnement $type = null;

    #[ORM\Column(type: 'float')]
    #[Assert\PositiveOrZero(message: "Le prix doit être positif.")]
    private float $prix = 0.0;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateInitiale = null;

    public function __construct()
    {
        $this->dateInitiale = new \DateTime(); // valeur par défaut = date système
    }

    public function getDateInitiale(): ?\DateTimeInterface
    {
        return $this->dateInitiale;
    }

    public function setDateInitiale(\DateTimeInterface $dateInitiale): self
    {
        $this->dateInitiale = $dateInitiale;

        return $this;
    }

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $dateExpiration;

    #[ORM\ManyToOne(targetEntity: Gym::class)]
    private ?Gym $gym = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $user = null;

    // --- GETTERS & SETTERS ---

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?TypeAbonnement
    {
        return $this->type;
    }

    public function setType(TypeAbonnement $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getPrix(): float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;
        return $this;
    }

    

    public function getDateExpiration(): \DateTimeInterface
    {
        return $this->dateExpiration;
    }

    public function setDateExpiration(\DateTimeInterface $dateExpiration): self
    {
        $this->dateExpiration = $dateExpiration;
        return $this;
    }

    public function getGym(): ?Gym
    {
        return $this->gym;
    }

    public function setGym(?Gym $gym): self
    {
        $this->gym = $gym;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }
}
