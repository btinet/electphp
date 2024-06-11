<?php

namespace App\Form;

use App\Entity\Election;
use App\Entity\Person;
use App\Entity\User;
use App\Repository\ElectionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonEditType extends AbstractType
{
    private ElectionRepository $electionRepository;

    public function __construct(ElectionRepository $repository)
    {
        $this->electionRepository = $repository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $person = $builder->getData();
        $elections = $this->electionRepository->findBy(['user' => $person->getUser()]);

        $builder
            ->add('user',TextType::class,[
                'attr' => ['readonly' => 'readonly'],
                'data_class' => User::class,
                'data' => $person->getUser(),
                'mapped' => false
            ])
            ->add('name')
            ->add('election',EntityType::class,[
                'class' => Election::class,
                'choices' => $elections
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
