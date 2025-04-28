<?php

namespace App\Repository;

use App\Entity\Cour;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cour>
 */
class CourRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cour::class);
    }

    /**
     * Vérifie s'il existe un cours qui utilise déjà la même salle dans le même créneau horaire.
     *
     * @param mixed $salle
     * @param \DateTimeInterface $dateDebut
     * @param \DateTimeInterface $dateFin
     * @param int|null $excludeId
     * @return Cour|null
     */
    public function findCoursBySalleAndCreneau($salle, $dateDebut, $dateFin, $excludeId = null): ?Cour
    {
        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.salle = :salle')
            ->andWhere('
                (c.dateDebut < :dateFin AND c.dateFin > :dateDebut)
            ')
            ->setParameter('salle', $salle)
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin);

        if ($excludeId) {
            $qb->andWhere('c.id != :excludeId')
               ->setParameter('excludeId', $excludeId);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }
    
}
