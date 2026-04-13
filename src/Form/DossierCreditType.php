<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DossierCreditType extends AbstractType
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
                'data'       => 13,
                'attr'       => [
                    'readonly' => true,
                    'step'     => '0.01',
                    'class'    => 'form-control',
                ],
                'label_attr' => ['class' => 'form-label'],
            ])
            ->add('objet_credit', ChoiceType::class, [
                'label'        => 'Objet du crédit',
                'choices'      => [
                    '-- Sélectionnez un objet --' => '',
                    'Logement'                    => 'logement',
                    'Véhicule'                    => 'vehicule',
                    'Éducation'                   => 'education',
                    'Santé'                       => 'sante',
                    'Commerce/Affaires'           => 'commerce',
                    'Autre'                       => 'autre',
                ],
                'placeholder'  => false,
                'attr'         => ['class' => 'form-select'],
                'label_attr'   => ['class' => 'form-label'],
            ])
            ->add('description', TextareaType::class, [
                'label'      => 'Description / Justification',
                'attr'       => [
                    'placeholder' => 'Décrivez les raisons et détails de votre demande',
                    'rows'        => 4,
                    'minlength'   => 10,
                    'maxlength'   => 500,
                    'class'       => 'form-control',
                ],
                'label_attr' => ['class' => 'form-label'],
            ])
            ->add('soumettre', SubmitType::class, [
                'label' => 'Soumettre la demande',
                'attr'  => ['class' => 'btn btn-primary'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DossierCreditData::class,
        ]);
    }
}
