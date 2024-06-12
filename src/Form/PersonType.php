<?php

namespace App\Form;

use App\Entity\Election;
use App\Entity\Person;
use App\Entity\User;
use App\Repository\ElectionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\User\UserInterface;

class PersonType extends AbstractType
{
    private ElectionRepository $electionRepository;
    private UserInterface $user;

    public function __construct(ElectionRepository $repository, Security $security)
    {
        $this->electionRepository = $repository;
        $this->user = $security->getUser();
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choices = $this->electionRepository->findBy(['user' => $this->user,'vote' => false]);

        $builder
            ->add('user',TextType::class,[
                'attr' => ['readonly' => 'readonly','disabled' => 'disabled'],
                'data' => $this->user,
                'data_class' => User::class
            ])
            ->add('name')
            ->add('election',EntityType::class,[
                'class' => Election::class,
                'choices' => $choices
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Person::class,
        ]);
    }
}
