<?php

namespace App\Repository;

use App\Entity\Participation; // Update this line
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Evenement;
use App\Entity\User;

class ParticipationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participation::class); 
    }
    public function findAvailableEvents(): array
{
    return $this->createQueryBuilder('e')
        ->where('e.dateFin >= :now')
        ->andWhere('e.statut = :status')
        ->setParameter('now', new \DateTime())
        ->setParameter('status', 'A_VENIR')
        ->orderBy('e.dateDebut', 'ASC')
        ->getQuery()
        ->getResult();
}

public function countAvailablePlaces(int $eventId): int
{
    $totalPlaces = $this->createQueryBuilder('e')
        ->select('e.nombreDePlaces')
        ->where('e.id = :id')
        ->setParameter('id', $eventId)
        ->getQuery()
        ->getSingleScalarResult();

    $reservedPlaces = $this->getEntityManager()
        ->createQuery('
            SELECT SUM(p.nombreDePlacesReservees) 
            FROM App\Entity\Participation p 
            WHERE p.evenement = :id')
        ->setParameter('id', $eventId)
        ->getSingleScalarResult();

    return $totalPlaces - ($reservedPlaces ?? 0);
}
public function findAllWithUserAndEvent()
{
    return $this->createQueryBuilder('p')
        ->leftJoin('p.user', 'u')
        ->addSelect('u')
        ->leftJoin('p.evenement', 'e')
        ->addSelect('e')
        ->orderBy('p.id', 'DESC')
        ->getQuery()
        ->getResult();
}
public function findByEvent(Evenement $evenement): array
{
    return $this->createQueryBuilder('p')
        ->andWhere('p.evenement = :evenement')
        ->setParameter('evenement', $evenement)
        ->getQuery()
        ->getResult();
}
public function findByUser(User $user): array
{
    return $this->createQueryBuilder('p')
        ->leftJoin('p.evenement', 'e')
        ->addSelect('e')
        ->where('p.user = :user')
        ->setParameter('user', $user)
        ->orderBy('p.id', 'DESC')
        ->getQuery()
        ->getResult();
}

public function findWithFilters(array $filters = []): array
{
    $qb = $this->createQueryBuilder('p')
        ->leftJoin('p.user', 'u')
        ->addSelect('u')
        ->leftJoin('p.evenement', 'e')
        ->addSelect('e');

    if (!empty($filters['user'])) {
        $qb->andWhere('u.id = :userId')
           ->setParameter('userId', $filters['user']);
    }

    if (!empty($filters['event'])) {
        $qb->andWhere('e.id = :eventId')
           ->setParameter('eventId', $filters['event']);
    }

    if (!empty($filters['dateFrom'])) {
        $qb->andWhere('e.dateDebut >= :dateFrom')
           ->setParameter('dateFrom', new \DateTime($filters['dateFrom']));
    }

    if (!empty($filters['dateTo'])) {
        $qb->andWhere('e.dateFin <= :dateTo')
           ->setParameter('dateTo', new \DateTime($filters['dateTo']));
    }

    return $qb->orderBy('p.id', 'DESC')
              ->getQuery()
              ->getResult();
}
}