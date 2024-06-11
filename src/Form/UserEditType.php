<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Unique;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname',TextType::class,[
                'required' => true,
            ])
            ->add('lastname',TextType::class,[
                'required' => true,
            ])
            ->add('email',EmailType::class,[
                'required' => false
            ])
            ->add('roles',ChoiceType::class,[
                'choices' => [
                    'ROLE_SUPER_ADMIN' => 'ROLE_SUPER_ADMIN',
                    'ROLE_ADMIN' => 'ROLE_ADMIN',
                    'ROLE_USER' => 'ROLE_USER'
                ],
                'multiple' => true,
                'expanded' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'constraints' => [
                new UniqueEntity([
                    'entityClass' => User::class,
                    'fields' => 'username',
                    'message' => 'Kennung wird bereits verwendet.'
                ]),
                new UniqueEntity([
                    'entityClass' => User::class,
                    'fields' => 'email',
                    'message' => 'E-Mail-Adresse wird bereits verwendet.'
                ]),
            ],
        ]);
    }
}
