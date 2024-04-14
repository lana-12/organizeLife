<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Project;
use App\Form\CollaboratorType;
use App\Service\CollaboratorService;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class CollaboratorController extends AbstractController
{
    public function __construct(
        
        private CollaboratorService $collaborator,
        private ProjectRepository $projectRepo,
        private EntityManagerInterface $em,
        private Security $security
    
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



    #[Route('/ajouter/{id}', name: 'collaborator.new', requirements: ['id' => '\d+', ])]
    public function new(Request $request, int $id, UserPasswordHasherInterface $userPasswordHasher): Response
    {

         /**
         * @var $user
         */
        $user = $this->security->getUser();
        $admin = $user->getId();

        if(!$this->security->getUser()){
            $this->addFlash('danger', "Vous devez être connecté(e) pour accéder à ce service");
            return $this->redirectToRoute('home');
        }

        if(!in_array("ROLE_ADMIN", $user->getRoles(), true )){
            $this->addFlash('danger', "Vous ne disposez pas des droits pour accéder à ce service");
            return $this->redirectToRoute('home');
        } 

        // Retrieve project by id paramater url
        $project = $this->projectRepo->find($id);

        // Gestion of the Collaborator
        $collaborator = new User();
        
        // dd($user);
        
        // Create User + dispaly Form
        $form = $this->createForm(CollaboratorType::class, $collaborator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            // dd($form->getData());

            if($request->getMethod() === "POST"){


                $collaborator->setFirstname(ucfirst($form->get('firstname')->getData()) );
                $collaborator->setLastname(ucfirst($form->get('lastname')->getData()) );

                $collaborator->setRoles(['ROLE_USER', 'ROLE_COLLABORATOR']);
                $collaborator->addProject($project);
                $project->addCollaborator($collaborator);
                $project->setAdmin($this->security->getUser());

                $collaborator->setPassword(
                    $userPasswordHasher->hashPassword(
                        $collaborator,
                        $form->get('password')->getData()
                    )
                );
                
                $this->em->persist($collaborator);
                $this->em->persist($project);
                $this->em->flush();
      

                $this->addFlash('success', "Collaborateur créé avec success !!");

                return $this->redirectToRoute('project.show', [
                    'id' => $project->getId(),
                    'slug' => $project->getSlug(),
                
                ]);

            }

        }

        return $this->render('collaborator/formNewCollaborator.html.twig', [
            'collaboratorform' => $form,
        ]);    
    
    }
}
