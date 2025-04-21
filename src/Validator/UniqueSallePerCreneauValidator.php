<?php
namespace App\Validator;

use App\Entity\Cour;
use App\Repository\CourRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueSallePerCreneauValidator extends ConstraintValidator
{
    private $courRepository;

    public function __construct(CourRepository $courRepository)
    {
        $this->courRepository = $courRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueSallePerCreneau) {
            throw new UnexpectedTypeException($constraint, UniqueSallePerCreneau::class);
        }

        /** @var Cour $cour */
        $cour = $this->context->getObject();

        if (!$cour->getSalle() || !$cour->getDateDebut() || !$cour->getDateFin()) {
            return;
        }

        $coursExistant = $this->courRepository->findCoursBySalleAndCreneau(
            $cour->getSalle(),
            $cour->getDateDebut(),
            $cour->getDateFin(),
            $cour->getId() // Pour ne pas comparer avec lui-même si on modifie
        );

        if ($coursExistant) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ salle }}', $cour->getSalle()->getNom())
                ->setParameter('{{ dateDebut }}', $cour->getDateDebut()->format('d/m/Y H:i'))
                ->setParameter('{{ dateFin }}', $cour->getDateFin()->format('d/m/Y H:i'))
                ->addViolation();
        }
    }
}
