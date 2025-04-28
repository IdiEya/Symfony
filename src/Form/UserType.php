<?php

namespace App\Form;

use App\Entity\Abonnement;
use App\Entity\Gym;
use App\Entity\Produit;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('role')
            ->add('email')
            ->add('password')
            ->add('prenom')
            ->add('nom')
            ->add('telephone')
            ->add('adresse')
            ->add('photo')
            ->add('specialite')
            ->add('note')
            ->add('abonnement', EntityType::class, [
                'class' => Abonnement::class,
                'choice_label' => 'id',
            ])
            ->add('gym', EntityType::class, [
                'class' => Gym::class,
                'choice_label' => 'id',
            ])
            ->add('produits', EntityType::class, [
                'class' => Produit::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
