<?php

namespace App\Form;

use App\Entity\Complaint;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ComplaintType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, [
                'choices'  => [
                    'Technical Issue' => 'Technical Issue',
                    'Billing & Payments' => 'Billing & Payments',
                    'Account Access' => 'Account Access',
                    'Security & Fraud' => 'Security & Fraud',
                    'Other' => 'Other',
                ],
                'placeholder' => 'Select a category...'
            ])
            ->add('title')
            ->add('description')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Complaint::class,
            'validation_groups' => ['Default', 'frontend'],
        ]);
    }
}
