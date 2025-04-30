<?php

namespace App\Form;

use App\Entity\Gym;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GymType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la salle',
                'attr' => ['class' => 'form-control']
            ])
            ->add('longitude', NumberType::class, [
                'label' => 'Longitude',
                'attr' => ['class' => 'form-control']
            ])
            ->add('latitude', NumberType::class, [
                'label' => 'Latitude',
                'attr' => ['class' => 'form-control']
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Photo de la salle',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control-file',
                    'accept' => 'image/jpeg,image/png'
                ]
            ])
            ->add('services', TextareaType::class, [
                'label' => 'Services proposés',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4
                ]
            ])
            ->add('horaires', TextType::class, [
                'label' => 'Horaires',
                'attr' => ['class' => 'form-control']
            ])
            ->add('contact', TextType::class, [
                'label' => 'Contact',
                'attr' => ['class' => 'form-control']
            ])
            ->add('prixMensuel', NumberType::class, [
                'label' => 'Prix mensuel (DT)',
                'required' => false,
                'attr' => ['class' => 'form-control price-input']
            ])
            ->add('prixTrimestriel', NumberType::class, [
                'label' => 'Prix trimestriel (DT)',
                'required' => false,
                'attr' => ['class' => 'form-control price-input']
            ])
            ->add('prixSemestriel', NumberType::class, [
                'label' => 'Prix semestriel (DT)',
                'required' => false,
                'attr' => ['class' => 'form-control price-input']
            ])
            ->add('prixAnnuel', NumberType::class, [
                'label' => 'Prix annuel (DT)',
                'required' => false,
                'attr' => ['class' => 'form-control price-input']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Gym::class,
            'attr' => [
                'novalidate' => 'novalidate', // Désactive la validation HTML5
                'class' => 'gym-form'
            ]
        ]);
    }
}
