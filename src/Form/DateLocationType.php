<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateLocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('startDateTime', DateTimeType::class, [
            'label'       => 'Date de début',
            'date_widget' => 'single_text',
            'time_widget' => 'single_text',
            'attr' => ['class' => 'startDateTime'],
        ])
        ->add('stopDateTime', DateTimeType::class, [
            'label'       => 'Date de fin',
            'date_widget' => 'single_text',
            'time_widget' => 'single_text',
            'attr' => ['class' => 'stopDateTime'],
        ])->add('pay', SubmitType::class, [
            'attr' => [
                'class' => 'btn btn-lg btn-primary text-uppercase'
            ],
        ])->add('reduction', SubmitType::class, [
            'attr' => [
                'class' => 'btn btn-lg btn-success text-uppercase'
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'      => null,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'location_item'
        ]);
    }
}
