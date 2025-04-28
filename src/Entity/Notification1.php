<?php
// src/Entity/Notification1.php
namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: "App\Repository\Notification1Repository")]
class Notification1
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private $user;

    #[ORM\Column(type: 'string')]
    private $message;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;
#[ORM\ManyToOne(targetEntity: Evenement::class)]
private $evenement;

    public function __construct() {
        $this->createdAt = new \DateTime();
    }

    // Getters et setters...
    public function getId(): ?int {
        return $this->id;
    }

    public function getUser(): ?User {
        return $this->user;
    }

    public function setUser(?User $user): self {
        $this->user = $user;
        return $this;
    }

    public function getMessage(): ?string {
        return $this->message;
    }

    public function setMessage(string $message): self {
        $this->message = $message;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface {
        return $this->createdAt;
    }


    // src/Entity/Notification1.php



// Add these getter and setter methods
public function getEvenement(): ?Evenement
{
    return $this->evenement;
}

public function setEvenement(?Evenement $evenement): self
{
    $this->evenement = $evenement;
    return $this;
}
}