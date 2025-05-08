<?php

namespace App\Entity;

use App\Repository\GymRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GymRepository::class)]
#[Vich\Uploadable]
class Gym
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le nom de la salle est obligatoire")]
    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $nom = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $longitude = null;

    #[Vich\UploadableField(mapping: "gym_images", fileNameProperty: "photo")]
    #[Assert\File(
        maxSize: "2M",
        mimeTypes: ["image/jpeg", "image/png"],
        mimeTypesMessage: "Veuillez uploader une image valide (JPEG ou PNG)"
    )]
    private ?File $imageFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: "La description des services est obligatoire")]
    private ?string $services = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Les horaires d'ouverture sont obligatoires")]
    private ?string $horaires = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le contact est obligatoire")]
    #[Assert\Regex(
        pattern: "/^[0-9+\s]+$/",
        message: "Seuls les chiffres, espaces et + sont autorisés"
    )]
    private ?string $contact = null;

    #[ORM\Column(type: 'float')]
    #[Assert\PositiveOrZero(message: "Le prix doit être positif ou nul")]
    private float $prixMensuel = 0.0;

    #[ORM\Column(type: 'float')]
    #[Assert\PositiveOrZero(message: "Le prix doit être positif ou nul")]
    private float $prixTrimestriel = 0.0;

    #[ORM\Column(type: 'float')]
    #[Assert\PositiveOrZero(message: "Le prix doit être positif ou nul")]
    private float $prixSemestriel = 0.0;

    #[ORM\Column(type: 'float')]
    #[Assert\PositiveOrZero(message: "Le prix doit être positif ou nul")]
    private float $prixAnnuel = 0.0;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): static
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): static
    {
        $this->longitude = $longitude;
        return $this;
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;
        return $this;
    }

    public function getServices(): ?string
    {
        return $this->services;
    }

    public function setServices(string $services): static
    {
        $this->services = $services;
        return $this;
    }

    public function getHoraires(): ?string
    {
        return $this->horaires;
    }

    public function setHoraires(string $horaires): static
    {
        $this->horaires = $horaires;
        return $this;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(string $contact): static
    {
        $this->contact = $contact;
        return $this;
    }

    public function getPrixMensuel(): float
    {
        return $this->prixMensuel;
    }

    public function setPrixMensuel(float $prixMensuel): static
    {
        $this->prixMensuel = $prixMensuel;
        return $this;
    }

    public function getPrixTrimestriel(): float
    {
        return $this->prixTrimestriel;
    }

    public function setPrixTrimestriel(float $prixTrimestriel): static
    {
        $this->prixTrimestriel = $prixTrimestriel;
        return $this;
    }

    public function getPrixSemestriel(): float
    {
        return $this->prixSemestriel;
    }

    public function setPrixSemestriel(float $prixSemestriel): static
    {
        $this->prixSemestriel = $prixSemestriel;
        return $this;
    }

    public function getPrixAnnuel(): float
    {
        return $this->prixAnnuel;
    }

    public function setPrixAnnuel(float $prixAnnuel): static
    {
        $this->prixAnnuel = $prixAnnuel;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function __toString(): string
    {
        return $this->nom ?? '';
    }

    public function getPhotoPath(): ?string
    {
        if (!$this->photo) {
            return null;
        }
        return '/uploads/gyms/' . $this->photo;
    }
}
