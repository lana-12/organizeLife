<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Event;
use App\Entity\Project;
use App\Entity\TypeEvent;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $defaultDate = new \DateTimeImmutable();

        $builder
            ->add('title', TextType::class,[
                'label'=> 'Titre', 
                'attr'=>[
                    'class'=>'form-control',
                ],
                'required' => true,

            ])
            ->add('date_event', DateType::class,[
                'label'=> 'Date', 
                'attr'=>[
                    'class'=>'form-control',
                ],
                'required' => true,
                'widget' => 'single_text',
                'input'  => 'datetime_immutable',
                'data' => $options['start_date'] ? new \DateTimeImmutable($options['start_date']) : null,
                // 'data' => $defaultDate,
            ])
            ->add('hour_event', TimeType::class, [
                'label'=> 'Heure', 
                'attr'=>[
                    'class'=>'form-control',
                ],
                'required' => true,
                'widget' => 'single_text',
                'input' => 'datetime',
            ])
            ->add('description', TextareaType::class, [
                'label'=> 'Description', 
                'attr'=>[
                    'class'=>'form-control',
                ],
            ])
            
            ->add('typeEvent', EntityType::class, [
                'label'=> 'Type', 
                'attr'=>[
                    'class'=>'form-control',
                ],
                'required' => true,
                'class' => TypeEvent::class,
                'choice_label' => 'name',
            ])


            ->add('user', EntityType::class, [
                'label'=> 'Collaborateur', 
                'attr'=>[
                    'class'=>'form-control',
                ],
                'required' => true,
                'class' => User::class,
                'choice_label' => 'firstname',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('u')
                        ->leftJoin('u.projects', 'p')
                        ->where('p.id = :projectId')
                        ->setParameter('projectId', $options['projectId']);
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            'projectId' => null,
            'start_date' => null,
        ]);
    }
}
