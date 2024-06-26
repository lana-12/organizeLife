<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Project;
use App\DTO\CollaboratorDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class CollaboratorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Son email',
                'attr'=>[
                    'class'=>'form-control',
                ],
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Son prénom',
                'attr'=>[
                    'class'=>'form-control',
                ],
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Son nom',
                'attr'=>[
                    'class'=>'form-control',
                ],
            ])
            ->add('save', SubmitType::class, [
                'label'=> 'Enregistrer',
                'attr'=>[
                    'class'=>'btn btn-primary ' ,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CollaboratorDTO::class,
        ]);
    }
}
