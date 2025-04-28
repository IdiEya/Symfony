<?php
// src/Repository/Notification1Repository.php
namespace App\Repository;

use App\Entity\Notification1;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Notification1|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification1|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification1[]    findAll()
 * @method Notification1[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class Notification1Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification1::class);
    }

    // Vous pouvez ajouter des méthodes personnalisées ici si nécessaire
}