<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cours
 *
 * @ORM\Table(name="cours", indexes={@ORM\Index(name="coach_id", columns={"coach_id"}), @ORM\Index(name="responsableSalle_id", columns={"responsableSalle_id"}), @ORM\Index(name="salle_id", columns={"salle_id"})})
 * @ORM\Entity
 */
class Cours
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
     * @var \DateTime
     *
     * @ORM\Column(name="dateDebut", type="date", nullable=false)
     */
    private $datedebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateFin", type="date", nullable=false)
     */
    private $datefin;

    /**
     * @var string
     *
     * @ORM\Column(name="localisation", type="string", length=255, nullable=false)
     */
    private $localisation;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var int|null
     *
     * @ORM\Column(name="coach_id", type="integer", nullable=true)
     */
    private $coachId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="responsableSalle_id", type="integer", nullable=true)
     */
    private $responsablesalleId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="salle_id", type="integer", nullable=true)
     */
    private $salleId;

    /**
     * @var string
     *
     * @ORM\Column(name="prix", type="decimal", precision=10, scale=2, nullable=false, options={"default"="0.00"})
     */
    private $prix = '0.00';

    /**
     * @var int|null
     *
     * @ORM\Column(name="salleId", type="integer", nullable=true)
     */
    private $salleid;

    /**
     * @var int|null
     *
     * @ORM\Column(name="placesDisponibles", type="integer", nullable=true, options={"default"="20"})
     */
    private $placesdisponibles = 20;


}
