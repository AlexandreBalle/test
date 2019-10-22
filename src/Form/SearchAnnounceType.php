<?php

namespace App\Form;

use App\Entity\Vehicle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchAnnounceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('minPrice', NumberType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Prix Mini'
                ],
            ])
            ->add('maxPrice', NumberType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Prix Max'
                ],
            ])
            ->add('type', ChoiceType::class, [
                'required' => false,
                'placeholder' => '----',
                'attr' => [
                    'class' => 'form-control',
                ],
                'choices'   => $this->getChoices()
            ])
        ;
    }

    public function getChoices()
    {
        $array = [];

        foreach (Vehicle::$types as $type) {
            $array[$type] = $type;
        }

        return $array;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'csrf_protection' => false,
        ]);
    }
}
