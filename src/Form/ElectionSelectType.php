<?php

namespace App\Form;

use App\Entity\Election;
use App\Entity\Person;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ElectionSelectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code',NumberType::class,[
                'label' => 'Wahlcode',
                'mapped' => false
            ])
            ->add('people')
            ->add('v2',EntityType::class,[
                    'label' => '2. Stimme',
                    'mapped' => false,
                    'class' => Person::class,
                    'multiple' => false,
                    'expanded' => false,
                ])
            ->add('v3',EntityType::class,[
                'label' => '3. Stimme',
                'mapped' => false,
                'class' => Person::class,
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('v4',EntityType::class,[
                'label' => '4. Stimme',
                'mapped' => false,
                'class' => Person::class,
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('submit',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Election::class,
        ]);
    }
}
