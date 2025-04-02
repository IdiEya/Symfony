<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\ParticipationRepository;

#[ORM\Entity(repositoryClass: ParticipationRepository::class)]
#[ORM\Table(name: 'participation')]
class Participation
{
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'participations')]
    #[ORM\JoinColumn(name: 'utilisateur_id', referencedColumnName: 'id')]
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

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $evenement_id = null;

    public function getEvenement_id(): ?int
    {
        return $this->evenement_id;
    }

    public function setEvenement_id(int $evenement_id): self
    {
        $this->evenement_id = $evenement_id;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $statutP = null;

    public function getStatutP(): ?string
    {
        return $this->statutP;
    }

    public function setStatutP(string $statutP): self
    {
        $this->statutP = $statutP;
        return $this;
    }

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

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $commentaire = null;

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): self
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $nombreDePlacesReservees = null;

    public function getNombreDePlacesReservees(): ?int
    {
        return $this->nombreDePlacesReservees;
    }

    public function setNombreDePlacesReservees(?int $nombreDePlacesReservees): self
    {
        $this->nombreDePlacesReservees = $nombreDePlacesReservees;
        return $this;
    }

    public function getEvenementId(): ?int
    {
        return $this->evenement_id;
    }

    public function setEvenementId(int $evenement_id): static
    {
        $this->evenement_id = $evenement_id;

        return $this;
    }

}
