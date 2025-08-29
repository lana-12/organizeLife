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
            ->add('date_event_start', DateType::class,[
                'label'=> 'Date de début', 
                'attr'=>[
                    'class'=>'form-control',
                ],
                'required' => true,
                'widget' => 'single_text',
                'input'  => 'datetime_immutable',
                // Valeur by default  => $defaultDate else start_date
                'data' => $options['start_date'] ? new \DateTimeImmutable($options['start_date']) : $defaultDate,
                // 'data' => $defaultDate,
            ])

            ->add('date_event_end', DateType::class,[
                'label'=> 'Date de fin', 
                'attr'=>[
                    'class'=>'form-control',
                ],
                'required' => true,
                'widget' => 'single_text',
                'input'  => 'datetime_immutable',
                // Valeur by default  => $defaultDate else start_date
                'data' => $options['start_date'] ? new \DateTimeImmutable($options['start_date']) : $defaultDate,
                // 'data' => $defaultDate,
            ])

            ->add('hour_event_start', TimeType::class, [
                'label'=> 'Heure de début', 
                'attr'=>[
                    'class'=>'form-control',
                ],
                'required' => true,
                'widget' => 'single_text',
                'input' => 'datetime',
            ])

            ->add('hour_event_end', TimeType::class, [
                'label'=> 'Heure de fin', 
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
                        ->leftJoin('App\Entity\Project', 'proj', 'WITH', 'proj.admin = u')
                        ->where('p.id = :projectId OR proj.id = :projectId')
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
            'end_date' => null,
            'admin' => null
        ]);
    }
}
