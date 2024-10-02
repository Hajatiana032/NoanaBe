<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Order;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'class' => 'form-control border shadow-none',
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'class' => 'form-control border shadow-none'
                ]
            ])
            ->add('phone', TelType::class, [
                'label' => 'Numéro de téléphone',
                'attr' => [
                    'class' => 'form-control border shadow-none'
                ],
                'constraints' => [
                    new Regex(pattern: '/^[0-9]{10}$/', message: 'Entrez un numéro de téléphone valide.')
                ]
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'attr' => [
                    'class' => 'form-control border shadow-none'
                ]
            ])
            ->add('city', EntityType::class, [
                'label' => 'ville',
                'class' => City::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-control border shadow-none',
                    'data-model' => 'selectedCity'
                ],
                'placeholder' => 'Sélectionner votre ville'
            ])
            ->add('payOnDelivery', CheckboxType::class, [
                'label' => 'Payer à la livraison',
                'attr' => [
                    'class' => 'shadow-none'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
