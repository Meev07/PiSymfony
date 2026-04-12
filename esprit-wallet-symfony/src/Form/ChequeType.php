<?php

namespace App\Form;

use App\Entity\Cheque;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChequeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('chequeNumber', null, [
                'attr' => ['readonly' => true],
                'help' => 'Generated automatically for security'
            ])
            ->add('amount', NumberType::class, [
                'label' => 'Amount (TND)',
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\NotBlank(),
                    new \Symfony\Component\Validator\Constraints\Positive(['message' => 'The amount must be a positive value']),
                    new \Symfony\Component\Validator\Constraints\GreaterThanOrEqual([
                        'value' => 1000, 
                        'message' => 'The minimum amount for a digital cheque is 1000 TND for security and liquidity reasons'
                    ]),
                    new \Symfony\Component\Validator\Constraints\LessThan(['value' => 500000, 'message' => 'Individual cheques are limited to 500,000 TND per transaction']),
                    new \Symfony\Component\Validator\Constraints\Type(['type' => 'numeric', 'message' => 'Please enter a valid numeric amount']),
                ],
                'attr' => [
                    'step' => '0.01',
                    'type' => 'number'
                ]
            ])
            ->add('receiverIban', null, [
                'label' => 'Receiver IBAN (Tunisian Format)',
                'help' => 'Standard format: TN59 XXXX XXXX XXXX XXXX XXXX',
                'attr' => ['placeholder' => 'TN59...'],
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\Regex([
                        'pattern' => '/^TN59[0-9]{20}$/i',
                        'message' => 'Invalid Tunisian IBAN format. Must start with TN59 followed by 20 digits.'
                    ])
                ]
            ])
            ->add('expirationDate', null, [
                'widget' => 'single_text',
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\GreaterThan(['value' => 'today', 'message' => 'Expiration date must be in the future']),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cheque::class,
        ]);
    }
}
