<?php

namespace App\Service;

use App\Entity\User;
use App\DTO\CollaboratorDTO;
use App\Form\CollaboratorType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class CollaboratorService extends AbstractController
{

    public function __construct(

        private CollaboratorDTO $collaboratorDTO,
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $userPasswordHasher, 
        private UserAuthenticatorInterface $userAuthenticator, 
        private UserAuthenticator $authenticator, 
        private EntityManagerInterface $em,
        private Security $security,

        
        
        )
    {}



    // public function getCollaboratorsByProject(int $projectId): array
    // {
    //     $collaborators = $this->userRepository->findByProjectId($projectId);

    //     $collaboratorDTOs = [];

    //     foreach ($collaborators as $collaborator) {
    //         $collaboratorDTO = new CollaboratorDTO();
    //         $collaboratorDTO->setId($collaborator->getId());
    //         $collaboratorDTO->setFirstname($collaborator->getFirstname());
    //         $collaboratorDTO->setLastname($collaborator->getLastname());
    //         $collaboratorDTO->setEmail($collaborator->getEmail());

    //         $collaboratorDTOs[] = $collaboratorDTO;
    //     }

    //     return $collaboratorDTOs;
    // }



    public function createCollaborator(Request $request): void
    {
         /**
         * @var User 
         */
        $user = $this->security->getUser();
        // Gestion of the Users
        dd($user);

        // Create User + dispaly Form       
        $user = new User();
        $form = $this->createForm(CollaboratorType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
dump($form );


        //     if($request->getMethod() === "POST"){
        //         $user->setRoles(['ROLE_USER', 'ROLE_COLLABORATOR']);


        //         // $user->setPassword(
        //         //     $this->userPasswordHasher->hashPassword(
        //         //         $user,
        //         //         $form->get('plainPassword')->getData()
        //         //     )
        //         // );




        //         $this->addFlash('success', "Collaborateur créé avec success !!");

            
        //     }
        // }


        // $user = $collaboratorDTO->user;
        // $plainPassword = uniqid();
        // $user->setPassword($this->passwordEncoder->encodePassword($user, $plainPassword));
        // $user->setRoles(['ROLE_USER', 'ROLE_COLLABORATOR']);

        // $project = $collaboratorDTO->project;
        // $project->addCollaborator($user);

        // $this->em->persist($user);
        // $this->em->persist($project);
        // $this->em->flush();

        // Vous pouvez ajouter ici la logique pour envoyer un e-mail au nouvel utilisateur avec son mot de passe
    }

    // return $this->render('collaborator/formNewCollaborator.html.twig', [
    //     'collaboratorform' => $form,
    // ]);
}

}
