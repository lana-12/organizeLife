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

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $defaultDate = new \DateTimeImmutable();

        $builder
            ->add('title')
            ->add('date_event', DateType::class,[
                
                'widget' => 'single_text',
                'input'  => 'datetime_immutable',
                'data' => $defaultDate,
            ])
            ->add('hour_event', TimeType::class, [
                'widget' => 'single_text',
                'input' => 'datetime',
            ])
    //             'attr' => [
    //     'class' => 'nameClass' ],
            ->add('description')
            
            ->add('typeEvent', EntityType::class, [
                'class' => TypeEvent::class,
                'choice_label' => 'name',
            ])


            ->add('user', EntityType::class, [
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
        ]);
    }
}
