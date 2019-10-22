<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastname', TextType::class, [
                'label'       => 'Nom :',
                'required'    => true,
                'attr'        => [
                    'class'   => 'form-control'
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ])->add('firstname', TextType::class, [
                'label'       => 'Prenom :',
                'required'    => true,
                'attr'        => [
                    'class'   => 'form-control'
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ])->add('birthdayDate', DateType::class, [
                'required' => true,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ],
            ])
            ->add('address', TextType::class, [
                'label'       => 'Addresse postal :',
                'required'    => true,
                'attr'        => [
                    'class'   => 'form-control'
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ])->add('zipcode', TextType::class, [
                'label'       => 'Code Postal :',
                'required'    => true,
                'attr'        => [
                    'class'   => 'form-control'
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ])->add('city', TextType::class, [
                'label'       => 'Ville :',
                'required'    => true,
                'attr'        => [
                    'class'   => 'form-control'
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ])->add('licenseDriving', TextType::class, [
                'label'       => 'Ville :',
                'required'    => true,
                'attr'        => [
                    'class'   => 'form-control'
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ])->add('email', EmailType::class, [
                'label'       => 'Email :',
                'required'    => true,
                'attr'        => [
                    'class'   => 'form-control'
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('phone', TextType::class, [
                'label'       => 'Ville :',
                'required'    => true,
                'attr'        => [
                    'class'   => 'form-control'
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
