<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Votes
 *
 * @ORM\Table(name="votes")
 * @ORM\Entity
 */
class Votes
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="evenement_id", type="integer", nullable=true)
     */
    private $evenementId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="utilisateur_id", type="integer", nullable=true)
     */
    private $utilisateurId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="note", type="integer", nullable=true)
     */
    private $note;


}
