<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    /**
     * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
     * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
     * @method Produit[]    findAll()
     * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
     */

    /**
     * Recherche les produits en stock faible (quantité inférieure ou égale à un seuil donné)
     *
     * @param int $threshold Seuil de quantité
     * @return Produit[] Liste des produits en stock faible
     */
    public function findLowStockProducts(int $threshold): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.quantite <= :threshold')
            ->andWhere('p.quantite > 0')
            ->setParameter('threshold', $threshold)
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche les produits par un terme donné (nom, référence ou catégorie)
     *
     * @param string $term Terme de recherche
     * @return Produit[] Liste des produits correspondant au terme
     */
    public function findBySearchTerm(string $term): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.nom LIKE :term OR p.ref LIKE :term OR p.categorie LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche les produits dont le nom contient un terme spécifique
     *
     * @param string $nom Terme du nom
     * @return Produit[] Liste des produits dont le nom contient le terme
     */
 // src/Repository/ProduitRepository.php

 

 // Add custom method for the LIKE search
 public function findByNomLike($nom)
 {
     return $this->createQueryBuilder('p')
         ->where('p.nom LIKE :nom')
         ->setParameter('nom', '%' . $nom . '%')
         ->getQuery()
         ->getResult();
 }

 public function searchByCriteria(?string $nom, ?string $reference, ?string $categorie): array
{
    $qb = $this->createQueryBuilder('p');

    if ($nom) {
        $qb->andWhere('p.nom LIKE :nom')
           ->setParameter('nom', '%' . $nom . '%');
    }

    if ($reference) {
        $qb->andWhere('p.reference LIKE :reference')
           ->setParameter('reference', '%' . $reference . '%');
    }

    if ($categorie) {
        $qb->andWhere('p.categorie LIKE :categorie')
           ->setParameter('categorie', '%' . $categorie . '%');
    }

    return $qb->getQuery()->getResult();
}

public function findByMultiCriteria(?string $nom, ?string $reference, ?string $categorie): array
{
    $qb = $this->createQueryBuilder('p');

    if ($nom) {
        $qb->andWhere('p.nom LIKE :nom')
           ->setParameter('nom', '%' . $nom . '%');
    }

    if ($reference) {
        $qb->andWhere('p.reference LIKE :reference')
           ->setParameter('reference', '%' . $reference . '%');
    }

    if ($categorie) {
        $qb->andWhere('p.categorie LIKE :categorie')
           ->setParameter('categorie', '%' . $categorie . '%');
    }

    return $qb->getQuery()->getResult();
}

public function findByFilters(?string $nom, ?string $reference, ?string $categorie): array
{
    $qb = $this->createQueryBuilder('p');

    if ($nom) {
        $qb->andWhere('p.nom LIKE :nom')
           ->setParameter('nom', '%' . $nom . '%');
    }

    if ($reference) {
        $qb->andWhere('p.reference LIKE :reference')
           ->setParameter('reference', '%' . $reference . '%');
    }

    if ($categorie) {
        $qb->andWhere('p.categorie LIKE :categorie')
           ->setParameter('categorie', '%' . $categorie . '%');
    }

    return $qb->getQuery()->getResult();
}

}
