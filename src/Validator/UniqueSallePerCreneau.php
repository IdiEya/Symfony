<?php
namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueSallePerCreneau extends Constraint
{
    public $message = 'La salle "{{ salle }}" est déjà réservée entre {{ dateDebut }} et {{ dateFin }}.';
}
