<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RolesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('roles', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'Super administrateur' => "ROLE_SUPER_ADMIN",
                    'Administrateur' => "ROLE_ADMIN",
                    'Client' => "ROLE_USER"
                ],
                'attr' => [
                    'class' => 'shadow-none border'
                ],
                'multiple' => false,
                'expanded' => false,
            ]);

        // ? Change roles array to string
        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesAsArray): string {
                    return implode(', ', $rolesAsArray);
                },
                function ($rolesAsString): array {
                    return explode(', ', $rolesAsString);
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
