<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('slug')
            ->add('admin', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('collaborator', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'lastname',
                'multiple' => true,
            ])
            ->add('save', SubmitType::class, [
                'label'=> 'Créer',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
