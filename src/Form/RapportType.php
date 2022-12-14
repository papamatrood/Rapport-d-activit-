<?php

namespace App\Form;

use App\Entity\Rapport;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class RapportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('installation', IntegerType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('interqualite', IntegerType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('interdepannage', IntegerType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('visite', IntegerType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('recuperation', IntegerType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('autre', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control-sm'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rapport::class,
        ]);
    }
}
