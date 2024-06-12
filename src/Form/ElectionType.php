<?php

namespace App\Form;

use App\Entity\Election;
use App\Entity\Option;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\User\UserInterface;

class ElectionType extends AbstractType
{
    private UserInterface $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user',TextType::class,[
                'attr' => ['readonly' => 'readonly','disabled' => 'disabled'],
                'data' => $this->user,
                'data_class' => User::class
            ])
            ->add('label')
            ->add('voices',NumberType::class,[
                'required' => true,
                'help' => 'Wie viele Kreuze darf jede:r machen? (gilt nicht für Abstimmungen)'
            ])
            ->add('code-amount',NumberType::class,[
                'mapped' => false,
                'required' => false,
                'help' => 'Ohne Angabe werden automatisch 100 Wahlcodes generiert.'
            ])
            ->add('vote')
            ->add('description')
            ->add('options',EntityType::class,[
                'class' => Option::class,
                'multiple' => true,
                'expanded' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Election::class,
        ]);
    }
}
