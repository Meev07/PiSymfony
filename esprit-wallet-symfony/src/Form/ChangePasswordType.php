<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Please enter your current password']),
                    new UserPassword(['message' => 'Incorrect current password']),
                ],
                'attr' => [
                    'placeholder' => '••••••••',
                    'data-controller' => 'live-validation',
                    'data-live-validation-required-value' => 'true'
                ]
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'The password fields must match.',
                'first_options'  => [
                    'label' => 'New Password',
                    'attr' => [
                        'placeholder' => 'Min. 8 characters',
                        'data-controller' => 'live-validation',
                        'data-live-validation-pattern-value' => '^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$',
                        'data-live-validation-message-value' => 'Must include uppercase, lowercase, and numbers',
                        'data-live-validation-min-value' => 8,
                        'data-live-validation-required-value' => 'true'
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirm New Password',
                    'attr' => [
                        'placeholder' => 'Repeat password',
                        'data-controller' => 'live-validation',
                        'data-live-validation-required-value' => 'true'
                    ]
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a new password']),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'New password must be at least {{ limit }} characters',
                        'max' => 4096,
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                        'message' => 'Password must include uppercase, lowercase, and numbers.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
