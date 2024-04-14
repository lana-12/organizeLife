<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\User;
use App\Form\CollaboratorType;
use App\Service\CollaboratorService;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class CollaboratorController extends AbstractController
{
    public function __construct(
        
        private CollaboratorService $collaborator,
        private ProjectRepository $projectRepo,
        private EntityManagerInterface $em,
        private Security $security,
    
    ){}

    
    #[Route('/project/{id}', name: 'collaborator.index')]
    public function index(int $id,): Response
    {
        // $collaborators = $this->collaborator->getCollaboratorsByProject($id);

        return $this->render('collaborator/index.html.twig', [
            // 'collaborators' => $collaborators,
        ]);

        return $this->render('collaborator/index.html.twig', [
        ]);
    }



    #[Route('/ajouter/{$id}', name: 'collaborator.new', requirements: ['id' => '\d+', ])]
    public function new(Request $request, int $id): Response
    {

        /**
         * @var User 
         */
        $user = $this->security->getUser();
        // Gestion of the Users
        dump($user);
        dd($this->projectRepo->find($id));

        // Gestion of the Users

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

        return $this->render('collaborator/formNewCollaborator.html.twig', [
            'collaboratorform' => $form,
        ]);    
    
    }
}
