<?php

namespace App\Form;

use App\Entity\Cour;
use App\Entity\Salle;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

class CourType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('localisation', TextType::class)
            ->add('description', TextType::class, [
                'required' => false, // Optional description
            ])
            ->add('prix', NumberType::class)
            ->add('placesDisponibles', IntegerType::class, [
                'constraints' => [
                    new GreaterThanOrEqual([ // Correct usage
                        'value' => 1,
                        'message' => 'Le nombre de places doit être au moins 1.'
                    ]),
                    new LessThanOrEqual([ // Correct usage
                        'value' => 20,
                        'message' => 'Le nombre de places ne peut pas être supérieur à 20.'
                    ])
                ]
            ])
            ->add('salle', EntityType::class, [
                'class' => Salle::class,
                'choice_label' => 'nom',
                'placeholder' => 'Choisissez une salle',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cour::class,
        ]);
    }
}
