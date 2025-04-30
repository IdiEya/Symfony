<?php

namespace App\Form;

use App\Entity\Abonnement;
use App\Enum\TypeAbonnement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AbonnementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Mensuel' => TypeAbonnement::MENSUEL,
                    'Trimestriel' => TypeAbonnement::TRIMESTRIEL,
                    'Semestriel' => TypeAbonnement::SEMESTRIEL,
                    'Annuel' => TypeAbonnement::ANNUEL,
                ],
                'placeholder' => 'Sélectionner un type',
            ])
            ->add('dateInitiale', DateType::class, [
                'widget' => 'single_text',
                'data' => new \DateTime(), 
                'required' => false, 
                // 'disabled' => true, 
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Abonnement::class,
        ]);
    }
}
