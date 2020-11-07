<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AppointmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('appointmentTime', DateTimeType::class, [
                'input' => 'datetime_immutable',
                'widget' => 'single_text',
                'attr' => [
                    'step' => 900,
                    'min' => (new \DateTimeImmutable())->format('Y-m-d')
                ]
            ])
            ->add('petName', TextType::class)
            ->add('ownerName', TextType::class)
            ->add('contactNumber', TextType::class)
            ->add('appointmentLength', ChoiceType::class, [
                'choices' => [
                    'single' => false,
                    'double' => true
                ]
            ])
            ->add('submit', SubmitType::class);
    }
}