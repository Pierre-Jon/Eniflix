<?php

namespace App\Form;

use App\Entity\Serie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SerieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la série',
                'required' => true,
            ])
            ->add('overview', TextAreaType::class)
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'En Cours' => 'Returning',
                    'Terminé' => 'Ended',
                    'Abandonné' => 'Canceled'
                ],
                'placeholder' => '-- Choisissez un statut --',
    ])
            ->add('vote')
            ->add('popularity')
            ->add('genre')
            ->add('firstAirDate', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('lastAirDate', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('backdrop')
            ->add('poster')
            ->add('submit', SubmitType::class)
//            ->add('tmdbId')
//            ->add('dateCreated')
//            ->add('dateModified')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Serie::class,
        ]);
    }
}
