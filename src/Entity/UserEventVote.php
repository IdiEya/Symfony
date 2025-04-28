<?php

// src/Entity/UserEventVote.php

namespace App\Entity;

use App\Entity\Evenement;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: "App\Repository\UserEventVoteRepository")]
#[ORM\UniqueConstraint(name: 'user_event_unique', columns: ['user_id', 'event_id'])]
class UserEventVote
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Evenement::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Evenement $event;

    #[ORM\Column(type: 'smallint')] // 1 for like, -1 for dislike
    private int $vote;

    public function __construct()
    {
        // Initialisation par défaut
        $this->vote = 0;
    }

    // ... getters and setters ...

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getEvent(): Evenement
    {
        return $this->event;
    }

    public function setEvent(Evenement $event): self
    {
        $this->event = $event;
        return $this;
    }

    public function getVote(): int
    {
        return $this->vote;
    }

    public function setVote(int $vote): self
    {
        $this->vote = $vote;
        return $this;
    }
}