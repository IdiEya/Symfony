<?php
// src/Form/ReservationFormType.php
namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Validator\Constraints as Assert;

class ReservationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nom', TextType::class, [
            'label' => 'Nom',
            'attr' => ['placeholder' => 'Entrez votre nom'],
            'constraints' => [
                new Assert\NotBlank(['message' => 'Le nom ne peut pas être vide.']),
                new Assert\Regex([
                    'pattern' => '/^[a-zA-Z\s]+$/',
                    'message' => 'Le nom ne peut contenir que des lettres et des espaces, pas de chiffres.'
                ]),
            ]
        ])
        
        ->add('prenom', TextType::class, [
            'label' => 'Prénom',
            'attr' => ['placeholder' => 'Entrez votre prénom'],
            'constraints' => [
                new Assert\NotBlank(['message' => 'Le prénom ne peut pas être vide.']),
                new Assert\Regex([ 
                    'pattern' => '/^[a-zA-Z\s]+$/',
                    'message' => 'Le prénom ne doit contenir que des lettres et des espaces.'
                ]),
            ]
        ])
        
        ->add('tel', TextType::class, [
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Regex([
                    'pattern' => '/^\+?\d+$/',
                    'message' => 'Le numéro de téléphone doit être au format international (ex: +1234567890)'
                ]),
            ]
        ])
        
        ->add('adresse', TextType::class, [
            'label' => 'Adresse',
            'required' => true,
            'attr' => [
                'readonly' => true,
                'class' => 'adresse-field' // Classe CSS personnalisée
            ],
            'label_attr' => [
                'class' => 'adresse-label' // Classe CSS personnalisée pour le label
            ]
        ])
        

        

        
        
       ->add('date_reservation', DateType::class, [
    'label' => 'Choisir une date',
    'widget' => 'single_text',
    'constraints' => [
        new Assert\NotBlank(['message' => 'La date ne peut pas être vide.']),
        new Assert\GreaterThan([
            'value' => 'today',
            'message' => 'La date de réservation doit être dans le futur.',
        ]),
    ]
])
->add('latitude', HiddenType::class)
->add('longitude', HiddenType::class);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
