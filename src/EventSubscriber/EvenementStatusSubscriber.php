<?php
namespace App\EventSubscriber;

use App\Entity\Evenement;
use App\Entity\Statut;

use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class EvenementStatusSubscriber implements EventSubscriberInterface
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
            Events::postLoad,
        ];
    }

    private function updateStatus(Evenement $evenement): void
    {
        $newStatus = $evenement->getDateFin() < new \DateTime()
            ? Statut::TERMINE->value
            : Statut::A_VENIR->value;
        
        $evenement->setStatut($newStatus);
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof Evenement) {
            $this->updateStatus($entity);
        }
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof Evenement) {
            $this->updateStatus($entity);
        }
    }

    public function postLoad(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof Evenement) {
            $this->updateStatus($entity);
        }
    }
}