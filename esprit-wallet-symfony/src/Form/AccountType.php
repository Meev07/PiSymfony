<?php

namespace App\Form;

use App\Entity\Account;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Checking Account' => 'CHECKING',
                    'Savings Account' => 'SAVINGS',
                    'Business Account' => 'BUSINESS',
                ],
                'attr' => ['class' => 'form-select']
            ])
            ->add('balance', NumberType::class, [
                'label' => 'Initial Balance (TND)',
                'attr' => [
                    'placeholder' => '0.00',
                    'type' => 'number',
                    'step' => '0.01'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Account::class,
        ]);
    }
}
