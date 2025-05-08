<?php

namespace App\Entity;
use App\Entity\Statut;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\EvenementRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: EvenementRepository::class)]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank(message: "Le nom de l'événement est obligatoire")]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères"
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z0-9\s\p{P}éèàçùêîôûäëïöüÿâãñõæœçÉÈÀÇÙÊÎÔÛÄËÏÖÜŸÂÃÑÕÆŒÇ]+$/u",
        message: "Le nom ne doit contenir que des lettres et espaces"
    )]
    private ?string $nom = null;

    #[ORM\Column(type: 'text', nullable: false)]
    #[Assert\NotBlank(message: "La description est obligatoire")]
    #[Assert\Length(
        min: 2,
        minMessage: "La description doit contenir au moins {{ limit }} caractères"
    )]
    private ?string $description = null;

    #[ORM\Column(name: 'dateDebut', type: 'date', nullable: false)]
    #[Assert\NotBlank(message: "La date de début est obligatoire")]
    #[Assert\GreaterThanOrEqual(
        "today",
        message: "La date de début doit être aujourd'hui ou dans le futur"
    )]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(name: 'dateFin', type: 'date', nullable: false)]
    #[Assert\NotBlank(message: "La date de fin est obligatoire")]
    #[Assert\GreaterThanOrEqual(
        propertyPath: "dateDebut",
        message: "La date de fin doit être après la date de début"
    )]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank(message: "La localisation est obligatoire")]
    private ?string $localisation = null;

    #[ORM\Column(type: 'float', nullable: false)]
    #[Assert\NotBlank(message: "Les frais sont obligatoires")]
    #[Assert\PositiveOrZero(message: "Les frais doivent être positifs ou nuls")]
    private ?float $frais = null;
    
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $photo = null;

    #[Vich\UploadableField(mapping: 'evenement_images', fileNameProperty: 'photo')]
   
    #[Assert\Image(
        maxSize: '2M',
        mimeTypes: ['image/jpeg', 'image/png', 'image/gif'],
        mimeTypesMessage: 'Veuillez uploader une image valide (JPEG, PNG ou GIF)'
    )]
    private ?File $imageFile = null;


    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    #[ORM\Column(name: 'nombreDePlaces', type: 'integer', nullable: true)]
    #[Assert\NotBlank(message: "Le nombre de places est obligatoire")]
    #[Assert\PositiveOrZero(message: "Le nombre de places doit être positif")]
    private ?int $nombreDePlaces = null;

    #[ORM\Column(name: 'statut', type: 'string', length: 50, nullable: false, options: ['default' => 'A_VENIR'])]
private ?string $statut = 'A_VENIR';

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;
        return $this;
    }

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(string $localisation): self
    {
        $this->localisation = $localisation;
        return $this;
    }

    public function getFrais(): ?float
    {
        return $this->frais;
    }

    public function setFrais(float $frais): self
    {
        $this->frais = $frais;
        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): self
    {
        $this->photo = $photo;
        return $this;
    }

    public function getNombreDePlaces(): ?int
    {
        return $this->nombreDePlaces;
    }

    public function setNombreDePlaces(?int $nombreDePlaces): self
{
    $this->nombreDePlaces = $nombreDePlaces;
    $this->updateStatut(); // Mettre à jour le statut après changement
    return $this;
}

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }
    #[ORM\OneToMany(targetEntity: Participation::class, mappedBy: 'evenement', cascade: ['remove'])]
    private Collection $participations; 
    
    public function __construct()
    {
        $this->participations = new ArrayCollection(); 
        $this->statut = Statut::A_VENIR->value;
    }


/**
 * @return Collection<int, Participation>
 */
public function getParticipations(): Collection
{
    return $this->participations;
}

public function addParticipation(Participation $participation): self
{
    if (!$this->participations->contains($participation)) {
        $this->participations->add($participation);
        $participation->setEvenement($this);
    }
    return $this;
}

public function removeParticipation(Participation $participation): self
{
    if ($this->participations->removeElement($participation)) {
        if ($participation->getEvenement() === $this) {
            $participation->setEvenement(null);
        }
    }
    return $this;
}


public function getPlacesReservees(): int
{
    $total = 0;
    foreach ($this->participations as $participation) {
        // Vérifier que la participation est confirmée si vous avez un statut
        if ($participation->getStatutP() === 'CONFIRME') { // Adaptez selon vos statuts
            $total += $participation->getNombreDePlacesReservees();
        }
    }
    return $total;
}


public function isComplet(): bool
{
    if ($this->nombreDePlaces === null) {
        return false; // Si pas de limite de places, jamais complet
    }
    
    return $this->getPlacesReservees() >= $this->nombreDePlaces;
}

public function updateStatut(): void
{
    if ($this->nombreDePlaces !== null) {
        $placesDisponibles = $this->nombreDePlaces - $this->getPlacesReservees();
        
        if ($placesDisponibles <= 0) {
            $this->statut = Statut::COMPLET->value;
        } elseif ($this->dateFin < new \DateTime()) {
            $this->statut = Statut::TERMINE->value;
        } else {
            $this->statut = Statut::A_VENIR->value;
        }
    }
}
// src/Entity/Evenement.php

#[ORM\Column(type: 'integer', options: ['default' => 0])]
private int $likes = 0;

#[ORM\Column(type: 'integer', options: ['default' => 0])]
private int $dislikes = 0;

// ... (dans la même classe)

public function getLikes(): int
{
    return $this->likes;
}

public function setLikes(int $likes): self
{
    $this->likes = $likes;
    return $this;
}

public function getDislikes(): int
{
    return $this->dislikes;
}

public function setDislikes(int $dislikes): self
{
    $this->dislikes = $dislikes;
    return $this;
}

public function incrementLikes(): self
{
    $this->likes++;
    return $this;
}

public function decrementLikes(): self
{
    $this->likes = max(0, $this->likes - 1);
    return $this;
}

public function incrementDislikes(): self
{
    $this->dislikes++;
    return $this;
}

public function decrementDislikes(): self
{
    $this->dislikes = max(0, $this->dislikes - 1);
    return $this;
}


// src/Entity/Evenement.php

public function userHasLiked(User $user): bool
{
    foreach ($this->votes as $vote) {
        if ($vote->getUser() === $user && $vote->getVote() === 1) {
            return true;
        }
    }
    return false;
}

public function userHasDisliked(User $user): bool
{
    foreach ($this->votes as $vote) {
        if ($vote->getUser() === $user && $vote->getVote() === -1) {
            return true;
        }
    }
    return false;
}


}