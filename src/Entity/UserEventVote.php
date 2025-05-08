<?php

// src/Entity/UserEventVote.php

namespace App\Entity;

use App\Entity\Evenement;
use App\Entity\User;
use Doctrine\DBAL\Types\Types;
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