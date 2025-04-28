<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Positive;

use Symfony\Component\Form\Extension\Core\Type\NumberType;

class Produit2Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['is_edit'];
        $builder
          // Formulaire ProduitType.php
->add('prix', NumberType::class, [
    'constraints' => [
        new NotNull([
            'message' => 'Le prix ne peut pas être vide.'
        ]),
        new Positive([
            'message' => 'Le prix doit être positif.'
        ])
    ]
])

            ->add('quantite')
            ->add('ref')
            ->add('nom')
            ->add('description')
            ->add('photo', FileType::class, [
                'mapped' => false,
                'required' => !$isEdit,
                'constraints' => $isEdit ? [] : [
                    new NotBlank(['message' => 'La photo est obligatoire']),
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPEG, PNG, GIF)',
                    ])
                ],
                'label' => 'Photo du produit'
            ])
            ->add('categorie')
            ->add('users', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('categories', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
            'is_edit' => false,
        ]);
    }
}
