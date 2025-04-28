<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\File;

class Produit1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isNew = $options['new_product'];

        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du produit',
                'constraints' => [
                    new NotBlank(['message' => 'Le nom du produit est obligatoire']),
                ],
            ])
            ->add('ref', TextType::class, [
                'label' => 'Référence',
                'constraints' => [
                    new NotBlank(['message' => 'La référence est obligatoire']),
                ],
            ])
            ->add('prix', NumberType::class, [
                'constraints' => [
                    new NotNull(['message' => 'Le prix ne peut pas être vide.']),
                    new Positive(['message' => 'Le prix doit être positif.'])
                ]
            ])
            ->add('quantite', NumberType::class, [
                'label' => 'Quantité en stock',
                'constraints' => [
                    new NotBlank(['message' => 'La quantité est obligatoire']),
                ],
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'placeholder' => 'Choisissez une catégorie',
                'constraints' => [
                    new NotBlank(['message' => 'La catégorie est obligatoire'])
                ],
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo (JPEG ou PNG)',
                'mapped' => false,
                'required' => $isNew,
                'constraints' => $isNew ? [
                    new NotBlank(['message' => 'La photo est obligatoire']),
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPEG ou PNG)',
                    ])
                ] : [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPEG ou PNG)',
                    ])
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'constraints' => [
                    new NotBlank(['message' => 'La description est obligatoire'])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
            'new_product' => false // Définition de l'option manquante
        ]);

        $resolver->setAllowedTypes('new_product', 'bool');
    }
}