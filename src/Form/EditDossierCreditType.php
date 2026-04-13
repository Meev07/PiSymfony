<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditDossierCreditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('montant_demande', NumberType::class, [
                'label'      => 'Montant du crédit demandé (DT)',
                'scale'      => 2,
                'attr'       => [
                    'placeholder' => 'Entrez le montant',
                    'min'         => 100,
                    'max'         => 500000,
                    'step'        => '0.01',
                    'class'       => 'form-control',
                ],
                'label_attr' => ['class' => 'form-label'],
            ])
            ->add('duree_mois', NumberType::class, [
                'label'      => 'Durée du crédit (mois)',
                'scale'      => 0,
                'attr'       => [
                    'placeholder' => 'Nombre de mois',
                    'min'         => 1,
                    'max'         => 360,
                    'class'       => 'form-control',
                ],
                'label_attr' => ['class' => 'form-label'],
            ])
            ->add('taux_interet', NumberType::class, [
                'label'      => "Taux d'intérêt annuel (%)",
                'scale'      => 2,
                'attr'       => [
                    'readonly' => true,
                    'step'     => '0.01',
                    'class'    => 'form-control',
                ],
                'label_attr' => ['class' => 'form-label'],
            ])
            ->add('soumettre', SubmitType::class, [
                'label' => 'Mettre à jour',
                'attr'  => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EditDossierCreditData::class,
        ]);
    }
}
