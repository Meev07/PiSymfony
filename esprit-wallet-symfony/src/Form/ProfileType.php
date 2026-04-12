<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'attr' => [
                    'data-controller' => 'live-validation',
                    'data-live-validation-pattern-value' => '^[a-zA-Z\s\-]+$',
                    'data-live-validation-message-value' => 'Letters, spaces, or hyphens only',
                    'data-live-validation-min-value' => 2,
                    'data-live-validation-required-value' => 'true'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'First name is required']),
                    new Length(['min' => 2, 'max' => 50]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z\s\-]+$/',
                        'message' => 'First name can only contain letters, spaces, or hyphens'
                    ]),
                ],
            ])
            ->add('lastName', TextType::class, [
                'attr' => [
                    'data-controller' => 'live-validation',
                    'data-live-validation-pattern-value' => '^[a-zA-Z\s\-]+$',
                    'data-live-validation-message-value' => 'Letters, spaces, or hyphens only',
                    'data-live-validation-min-value' => 2,
                    'data-live-validation-required-value' => 'true'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Last name is required']),
                    new Length(['min' => 2, 'max' => 50]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z\s\-]+$/',
                        'message' => 'Last name can only contain letters, spaces, or hyphens'
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'data-controller' => 'live-validation',
                    'data-live-validation-pattern-value' => '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}$',
                    'data-live-validation-message-value' => 'Invalid email format',
                    'data-live-validation-required-value' => 'true'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Email is required']),
                    new Email(['message' => 'Invalid email format']),
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
