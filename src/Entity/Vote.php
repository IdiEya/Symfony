<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\VoteRepository;

#[ORM\Entity(repositoryClass: VoteRepository::class)]
#[ORM\Table(name: 'votes')]
class Vote
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

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $evenement_id = null;

    public function getEvenement_id(): ?int
    {
        return $this->evenement_id;
    }

    public function setEvenement_id(?int $evenement_id): self
    {
        $this->evenement_id = $evenement_id;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $utilisateur_id = null;

    public function getUtilisateur_id(): ?int
    {
        return $this->utilisateur_id;
    }

    public function setUtilisateur_id(?int $utilisateur_id): self
    {
        $this->utilisateur_id = $utilisateur_id;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $note = null;

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(?int $note): self
    {
        $this->note = $note;
        return $this;
    }

    public function getEvenementId(): ?int
    {
        return $this->evenement_id;
    }

    public function setEvenementId(?int $evenement_id): static
    {
        $this->evenement_id = $evenement_id;

        return $this;
    }

    public function getUtilisateurId(): ?int
    {
        return $this->utilisateur_id;
    }

    public function setUtilisateurId(?int $utilisateur_id): static
    {
        $this->utilisateur_id = $utilisateur_id;

        return $this;
    }

}
