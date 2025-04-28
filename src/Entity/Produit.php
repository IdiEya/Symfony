<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\ProduitRepository;
use App\Entity\Categorie;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
#[ORM\Table(name: 'produit')]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank(message: "Le nom ne peut pas être vide")]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $nom = null;

    #[ORM\Column(type: 'float', nullable: false)]
    #[Assert\NotBlank(message: "Le prix est obligatoire")]
    #[Assert\Positive(message: "Le prix doit être positif")]
    private ?float $prix = null;

    #[ORM\Column(type: 'integer', nullable: false)]
    #[Assert\NotBlank(message: "La quantité est obligatoire")]
    #[Assert\PositiveOrZero(message: "La quantité ne peut pas être négative")]
    private ?int $quantite = null;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank(message: "La référence est obligatoire")]
    #[Assert\Length(
        min: 3,
        max: 20,
        minMessage: "La référence doit contenir au moins {{ limit }} caractères",
        maxMessage: "La référence ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $ref = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\NotBlank(message: "La description est obligatoire")]
    #[Assert\Length(
        max: 1000,
        maxMessage: "La description ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
  
    #[Assert\File(
        maxSize: "5M",
        mimeTypes: ["image/jpeg", "image/png", "image/gif"],
        mimeTypesMessage: "Veuillez uploader une image valide (JPEG, PNG, GIF)"
    )]
    private ?string $photo = null;

    #[ORM\ManyToOne(targetEntity: Categorie::class, inversedBy: 'produits')]
    #[ORM\JoinColumn(name: 'categorie_id', referencedColumnName: 'id', nullable: false)]
    #[Assert\NotNull(message: "Veuillez sélectionner une catégorie")]
    private ?Categorie $categorie = null;

    #[ORM\OneToMany(targetEntity: Commande::class, mappedBy: 'produit')]
    private Collection $commandes;


private Collection $users;


    public function __construct()
    {
        $this->commandes = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    // Getters & Setters (identiques à ton code, sauf pour categories supprimé)

    public function getId(): ?int { return $this->id; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }

    public function getPrix(): ?float { return $this->prix; }
    public function setPrix(float $prix): self { $this->prix = $prix; return $this; }

    public function getQuantite(): ?int { return $this->quantite; }
    public function setQuantite(int $quantite): self { $this->quantite = $quantite; return $this; }

    public function getRef(): ?string { return $this->ref; }
    public function setRef(string $ref): self { $this->ref = $ref; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }

    public function getPhoto(): ?string { return $this->photo; }
    public function setPhoto(?string $photo): self { $this->photo = $photo; return $this; }

    public function getCategorie(): ?Categorie { return $this->categorie; }
    public function setCategorie(?Categorie $categorie): self { $this->categorie = $categorie; return $this; }

    public function getCommandes(): Collection { return $this->commandes; }
    public function addCommande(Commande $commande): self {
        if (!$this->commandes->contains($commande)) {
            $this->commandes[] = $commande;
            $commande->setProduit($this);
        }
        return $this;
    }
    public function removeCommande(Commande $commande): self {
        if ($this->commandes->removeElement($commande)) {
            if ($commande->getProduit() === $this) {
                $commande->setProduit(null);
            }
        }
        return $this;
    }

    public function getUsers(): Collection { return $this->users; }
    public function addUser(User $user): self {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }
        return $this;
    }
    public function removeUser(User $user): self {
        $this->users->removeElement($user);
        return $this;
    }
}
