<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreditSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('search_duree', IntegerType::class, [
                'label' => 'Rechercher par durée (mois)',
                'required' => false,
                'attr' => [
                    'min' => 1,
                    'step' => 1,
                    'placeholder' => 'Ex: 24',
                    'class' => 'form-control',
                ],
                'label_attr' => ['class' => 'form-label mb-1'],
            ])
            ->add('rechercher', SubmitType::class, [
                'label' => 'Recherche',
                'attr' => ['class' => 'btn btn-primary w-100'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CreditSearchData::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'credit_search',
        ]);
    }
}
