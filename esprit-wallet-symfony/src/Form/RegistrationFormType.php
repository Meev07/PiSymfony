<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', null, [
                'attr' => [
                    'data-controller' => 'live-validation',
                    'data-live-validation-pattern-value' => '^[a-zA-Z\s\-]+$',
                    'data-live-validation-message-value' => 'Letters, spaces, or hyphens only',
                    'data-live-validation-min-value' => 2,
                    'data-live-validation-required-value' => 'true'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'First name is required for verification']),
                    new Length(['min' => 2, 'max' => 50]),
                    new \Symfony\Component\Validator\Constraints\Regex([
                        'pattern' => '/^[a-zA-Z\s\-]+$/',
                        'message' => 'First name can only contain letters, spaces, or hyphens'
                    ]),
                ],
            ])
            ->add('lastName', null, [
                'attr' => [
                    'data-controller' => 'live-validation',
                    'data-live-validation-pattern-value' => '^[a-zA-Z\s\-]+$',
                    'data-live-validation-message-value' => 'Letters, spaces, or hyphens only',
                    'data-live-validation-min-value' => 2,
                    'data-live-validation-required-value' => 'true'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Last name is required for verification']),
                    new Length(['min' => 2, 'max' => 50]),
                    new \Symfony\Component\Validator\Constraints\Regex([
                        'pattern' => '/^[a-zA-Z\s\-]+$/',
                        'message' => 'Last name can only contain letters, spaces, or hyphens'
                    ]),
                ],
            ])
            ->add('email', null, [
                'attr' => [
                    'data-controller' => 'live-validation',
                    'data-live-validation-pattern-value' => '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}$',
                    'data-live-validation-message-value' => 'Invalid institutional email format',
                    'data-live-validation-required-value' => 'true'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Institutional email is strictly required for registration']),
                    new \Symfony\Component\Validator\Constraints\Email([
                        'message' => 'Invalid institutional email format. Please check your spelling.'
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You must agree to our security and privacy terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', \Symfony\Component\Form\Extension\Core\Type\RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => [
                    'autocomplete' => 'new-password', 
                    'class' => 'w-full bg-slate-50 border border-slate-100 outline-none p-4 rounded-2xl font-bold text-slate-700 text-sm focus:ring-2 ring-primary transition border shadow-sm',
                    'data-controller' => 'live-validation',
                    'data-live-validation-pattern-value' => '^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$',
                    'data-live-validation-message-value' => 'Must include uppercase, lowercase, and numbers',
                    'data-live-validation-min-value' => 8,
                    'data-live-validation-required-value' => 'true'
                ]],
                'required' => true,
                'first_options'  => ['label' => 'Private Password'],
                'second_options' => ['label' => 'Confirm Password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please establish a complex password for security reasons',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Security policy requires at least {{ limit }} characters',
                        'max' => 4096,
                    ]),
                    new \Symfony\Component\Validator\Constraints\Regex([
                        'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                        'message' => 'Your password is too weak. Must include uppercase, lowercase, and numbers.',
                    ]),
                ],
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
