<?php

namespace App\Repository;

use App\Entity\Evenement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Evenement>
 */
class EvenementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evenement::class);
    }
    public function findUpcomingEvents(): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.statut IN (:status)')
            ->setParameter('status', ['A_VENIR'])
            ->orderBy('e.dateDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function findAvailableEvents(): array
{
    return $this->createQueryBuilder('e')
        ->where('e.statut IN (:status)')
        ->andWhere('e.nombreDePlaces IS NULL OR e.nombreDePlaces > 0')
        ->setParameter('status', ['A_VENIR', 'TERMINE', 'COMPLET'])
        ->orderBy('e.dateDebut', 'ASC')
        ->getQuery()
        ->getResult();
}

public function findByName(string $name): array
{
    return $this->createQueryBuilder('e')
        ->where('e.nom LIKE :name')
        ->setParameter('name', '%' . $name . '%')
        ->getQuery()
        ->getResult();
}

public function searchByNameAndDescription(string $query): array
{
    return $this->createQueryBuilder('e')
        ->where('e.nom LIKE :query')
        ->orWhere('e.description LIKE :query')
        ->setParameter('query', '%'.$query.'%')
        ->getQuery()
        ->getResult();
}
public function findAllSorted(string $sortBy = 'nom', string $direction = 'asc'): array
{
    $validSorts = ['nom', 'dateDebut', 'dateFin', 'localisation', 'frais', 'nombreDePlaces'];
    $validDirections = ['asc', 'desc'];
    
    if (!in_array($sortBy, $validSorts)) {
        $sortBy = 'nom';
    }
    
    if (!in_array($direction, $validDirections)) {
        $direction = 'asc';
    }
    
    return $this->createQueryBuilder('e')
        ->orderBy('e.'.$sortBy, $direction)
        ->getQuery()
        ->getResult();
}
public function advancedSearch(string $query): array
{
    $qb = $this->createQueryBuilder('e');
    
    return $qb
        ->where($qb->expr()->orX(
            $qb->expr()->like('e.nom', ':query'),
            $qb->expr()->like('e.description', ':query'),
            $qb->expr()->like('e.localisation', ':query'),
            $qb->expr()->like('e.statut', ':query')
        ))
        ->setParameter('query', '%'.addcslashes($query, '%_').'%')
        ->getQuery()
        ->getResult();
}
    //    /**
    //     * @return Evenement[] Returns an array of Evenement objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Evenement
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}