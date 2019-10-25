<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

use App\Entity\Vehicle;

class RentalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('type', ChoiceType::class, [
            'label'      => 'Type :',
            'required'   => true,
            'attr'       => [
                'class'  => 'form-control'
            ],
            'choices'    => $this->getChoices()
        ])->add('brand', TextType::class, [
            'label'       => 'Marque :',
            'required'    => true,
            'attr'        => [
                'class'   => 'form-control'
            ],
            'constraints' => [
                new NotBlank(),
            ],
        ])->add('model', TextType::class, [
            'label'       => 'Modèle :',
            'required'    => true,
            'attr'        => [
                'class'   => 'form-control'
            ],
            'constraints' => [
                new NotBlank(),
            ],
        ])->add('photo', FileType::class, [
            'label'       => 'Photo du véhicule :',
            'data_class'  => null,
            'required'    => false,
            'attr'        => [
                'id'      => 'customFile',
                'class' => 'custom-file-input'
            ],
            'constraints' => [
                new NotBlank(),
            ],
        ])->add('km', IntegerType::class, [
            'label'       => 'Kilometrage :',
            'required'    => true,
            'attr'        => [
                'class'   => 'form-control'
            ],
            'constraints' => [
                new NotBlank(),
            ],
        ])->add('matriculation', TextType::class, [
            'label' => 'Matriculation :',
            'required' => true,
            'attr' => [
                'class' => 'form-control'
            ],
            'constraints' => [
                new NotBlank(),
            ],
        ])->add('year', DateType::class, [
            'label' => 'Année d\'immatriculation :',
            'required' => true,
            'widget' => 'single_text',
            'attr' => [
                'class' => 'form-control'
            ],
            'constraints' => [
                new NotBlank(),
            ],
        ])->add('autonomy', IntegerType::class, [
            'label' => 'Autonomie :',
            'required' => true,
            'attr' => [
                'class' => 'form-control'
            ],
            'constraints' => [
                new NotBlank(),
            ],
        ]);

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $this->isCar(
                    $event->getForm(),
                    $event->getData()->getType()
                );
            }
        );

        $builder->get('type')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $this->isCar(
                    $event->getForm()->getParent(),
                    $event->getForm()->getData()
                );
            }
        );
    }

    public function isCar(FormInterface $form, $type)
    {
        if ($type === Vehicle::$types[0]) {
            $form->add('door', IntegerType::class, [
                'label' => 'Nombre de portes :',
                'attr'  => [
                    'class' => 'form-control'
                ],
            ])->add('place', IntegerType::class, [
                'label' => 'Nombre de place :',
                'attr'  => [
                    'class' => 'form-control'
                ],
            ]);
        }
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
            'data_class'      => Vehicle::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'rental_item',
        ]);
    }
}
