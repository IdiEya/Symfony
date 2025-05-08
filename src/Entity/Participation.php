<?php

namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ParticipationRepository;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: ParticipationRepository::class)]
class Participation
{


        // Define the 'user' property for the relationship with User entity
        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'participations')]
        #[ORM\JoinColumn(name: 'utilisateur_id', referencedColumnName: 'id')]
        private ?User $user = null;


    #[ORM\Id]
    #[ORM\GeneratedValue] // Cette ligne est cruciale
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

 

    #[ORM\ManyToOne(targetEntity: Evenement::class, inversedBy: 'participations')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')] // Ajout de onDelete
    private ?Evenement $evenement = null;

    #[ORM\Column(name: 'statutP', type: 'string', length: 50)]
    private string $statutP = 'EN_ATTENTE';

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le commentaire ne peut pas dépasser {{ limit }} caractères"
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z0-9\s\p{P}éèàçùêîôûäëïöüÿâãñõæœçÉÈÀÇÙÊÎÔÛÄËÏÖÜŸÂÃÑÕÆŒÇ]+$/u",
        message: "Le commentaire contient des caractères non autorisés"
    )]
    private ?string $commentaire = null;

    #[ORM\Column(name: 'nombreDePlacesReservees', type: 'integer', nullable: false)]
#[Assert\NotNull(message: "Le nombre de places réservées est obligatoire")]
#[Assert\Type(type: 'integer', message: "La valeur {{ value }} doit être un nombre entier")]
#[Assert\Positive(message: "Le nombre de places doit être supérieur à 0")]
private ?int $nombreDePlacesReservees = null; // Changez ici
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

 

    public function getEvenement(): ?Evenement 
    {
        return $this->evenement;
    }
    
    public function setEvenement(?Evenement $evenement): self 
    {
        $this->evenement = $evenement;
        return $this;
    }

    public function getStatutP(): ?string
    {
        return $this->statutP;
    }

    public function setStatutP(string $statutP): self
    {
        $this->statutP = $statutP;
        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): self
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    public function getNombreDePlacesReservees(): ?int
{
    return $this->nombreDePlacesReservees;
}

public function setNombreDePlacesReservees(?int $nombreDePlacesReservees): self
{
    $this->nombreDePlacesReservees = $nombreDePlacesReservees;
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