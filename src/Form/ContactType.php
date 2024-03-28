<?php

namespace App\Form;

use App\Entity\User;
use App\DTO\ContactDTO;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ContactType extends AbstractType
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'empty_data'=>'',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'empty_data' => '',
            ])
            ->add('subject', TextType::class, [
                'label' => 'Sujet',
                'empty_data' => '',
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'empty_data' => '',
            ])
            

            ->add('collaborators', EntityType::class, [
                'label' => 'Collaborateurs',
                'class' => User::class,
                'multiple' => true,
                'expanded' => true,
                'choice_label' => 'email',
                'choices' => $this->userRepository->findAllUsersWithRoleUser(),
            ])
            // ->add('collaborators', CollectionType::class, [
            //     'label' => 'Collaborateurs',
            //     'entry_type' => EmailType::class,
            //     'allow_add' => true,
            //     'allow_delete' => true,
            // ])
            // ->add('users', ChoiceType::class, [
            //     'label'=> 'Joindre en copie',
            //     'choices'  => [
            //         'Compta' => 'compta@compta.fr',
            //         'Support' => 'support@support.fr',
            //         'Commerciaux' => 'commerciaux@commerciaux.fr',
            //     ],
            // ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactDTO::class,
        ]);
    }
}
