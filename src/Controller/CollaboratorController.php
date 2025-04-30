<?php

namespace App\Controller;

use App\DTO\CollaboratorDTO;
use App\Service\CheckService;
use App\Form\CollaboratorType;
use App\Service\MailerService;
use App\Repository\UserRepository;
use App\Service\CollaboratorService;
use App\Repository\ProjectRepository;
use App\Service\TextFormatterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/collaborators')]
#[IsGranted('ROLE_ADMIN')] 
class CollaboratorController extends AbstractController
{
    public function __construct(
        private CollaboratorService $collaboratorService,
        private ProjectRepository $projectRepo,
        private UserRepository $userRepo,
        private EntityManagerInterface $em,
        private Security $security,
        private MailerService $mailer,
        private CheckService $checkService
    ){}

    #[Route('/manage/{id}', name: 'collaborator.manage', requirements: ['id' => '\d+', ])]
    public function manageCollaborator(Request $request, int $id): Response
    {

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $admin = $user->getId();

        if(!$this->security->getUser()){
            $this->addFlash('danger', "Vous devez être connecté(e) pour accéder à ce service");
            return $this->redirectToRoute('home');
        }

        // Check if user is Admin (CheckService)
        if(!$this->checkService->checkAdminAccess()){
            $this->addFlash('danger', "Vous ne disposez pas des droits pour accéder à ce service");
            return $this->redirectToRoute('home');
        } 
        
        // Retrieve project by id paramater url
        $project = $this->projectRepo->find($id);
        if(!$project){
            $this->addFlash('danger', "Le projet n'existe pas ");
            return $this->redirectToRoute('admin.index');
        }
        return $this->render('collaborator/_manageCollaborator.html.twig', [
            'project' => $project,
        ]);    
    
    }

    #[Route('/ajouter/{id}', name: 'collaborator.new', requirements: ['id' => '\d+', ])]
    public function new(Request $request, int $id): Response
    {

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $admin = $user->getId();

        if(!$this->security->getUser()){
            $this->addFlash('danger', "Vous devez être connecté(e) pour accéder à ce service");
            return $this->redirectToRoute('home');
        }

        // Check if user is Admin (CheckService)
        if(!$this->checkService->checkAdminAccess()){
            $this->addFlash('danger', "Vous ne disposez pas des droits pour accéder à ce service");
            return $this->redirectToRoute('home');
        } 
        
        // Retrieve project by id paramater url
        $project = $this->projectRepo->find($id);
        if(!$project){
            $this->addFlash('danger', "Le projet n'existe pas ");
            return $this->redirectToRoute('admin.index');
        }

        // Gestion of the CollaboratorDTO
        $collaboratorDTO = new CollaboratorDTO();
        $form = $this->createForm(CollaboratorType::class, $collaboratorDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->collaboratorService->createCollaborator($collaboratorDTO, $project);
            // from=> defaultEmail
            $this->mailer->sendEmail(
                    $project->getAdmin()->getEmail(),
                    null,
                    'Bienvenue sur ' . $project->getName(),
                    'emails/newCollaborator.html.twig',
                    [
                        'project' => $project,
                        'collaborator' =>$data
                    ]
                );
            $this->addFlash('success', 'Collaborateur créé avec succès !!');
            return $this->redirectToRoute('project.show', [
                'id' => $project->getId(),
                'slug' => $project->getSlug(),
            ]);
        }
        return $this->render('collaborator/_formCollaborator.html.twig', [
            'collaboratorform' => $form,
            'project' => $project,
            'editMode'=> null,

        ]);    
    
    }

    /**
     * Edit Project 
     */
    #[Route('/edit/{project}/{id}', name: 'collaborator.edit', requirements: ['id' => '\d+', ])]
    public function editCollaborator(Request $request,int $project, int $id): Response
    {

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $admin = $user->getId();

        if(!$this->security->getUser()){
            $this->addFlash('danger', "Vous devez être connecté(e) pour accéder à ce service");
            return $this->redirectToRoute('home');
        }

        // Check if user is Admin (CheckService)
        if(!$this->checkService->checkAdminAccess()){
            $this->addFlash('danger', "Vous ne disposez pas des droits pour accéder à ce service");
            return $this->redirectToRoute('home');
        } 

        $projectAdmin = $this->projectRepo->find($project);
        if (!$projectAdmin) {
            $this->addFlash('danger', "Le projet n'existe pas ");
            return $this->redirectToRoute('admin.index');
        }
        
        $collaborator = $this->userRepo->find($id);
        if (!$collaborator) {
            $this->addFlash('danger', "Collaborateur non trouvé");
            return $this->redirectToRoute('project.show', [
                    'id' => $projectAdmin->getId(),
                    'slug' => $projectAdmin->getSlug(),
                ]);
        }

        $collaboratorDTO = $this->collaboratorService->buildCollaboratorDTOFromUser($collaborator, $projectAdmin);

        $form = $this->createForm(CollaboratorType::class, $collaboratorDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $collaborator->setFirstname(TextFormatterService::formatUcFirst($data->getFirstname()));
                $collaborator->setLastname(TextFormatterService::formatUcFirst($data->getLastname()));
                $collaborator->setEmail($data->getEmail());

                $this->em->persist($collaborator);
                $this->em->flush();

                $this->addFlash('success', 'Collaborateur modifié avec succès !!');
                return $this->redirectToRoute('project.show', [
                    'id' => $projectAdmin->getId(),
                    'slug' => $projectAdmin->getSlug(),
                ]);
            }

        return $this->render('collaborator/_formCollaborator.html.twig', [
            'collaboratorform' => $form,
            'project' => $projectAdmin,
            'editMode'=> true,
        ]);    
    }


    /**
     * Delete Collaborator 
     */
    #[Route('/supprimer/{project}/{id}', name: 'collaborator.delete', methods: ['POST', 'GET'])]
    public function deleteCollaborator(int $id, int $project)
    {
        $user = $this->userRepo->find($id);
        $project = $this->projectRepo->find($project);

        // Check if user is Admin (CheckService)
        if(!$this->checkService->checkAdminAccess()){
            $this->addFlash('danger', "Vous ne disposez pas des droits pour accéder à ce service");
            return $this->redirectToRoute('home');
        } 

        if (!$user || !$project) {
            $this->addFlash('danger', 'Collaborateur ou projet introuvable.');
            return $this->redirectToRoute('admin.index');
        }

        // Do not delete Admin here
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $this->addFlash('danger', "Impossible de supprimer un administrateur.");
            return $this->redirectToRoute('project.show', [
                'id' => $project->getId(),
                'slug' => $project->getSlug(),
            ]);
        }

        // Do not delete the collaborator if they are not part of the project
        if (!$project->getCollaborator()->contains($user)) {
            $this->addFlash('danger', "Cet utilisateur n'est pas collaborateur sur ce projet.");
            return $this->redirectToRoute('project.show', [
                'id' => $project->getId(),
                'slug' => $project->getSlug(),
            ]);
        }

        // Delete Collaborator for the project
        $project->removeCollaborator($user);
        $this->em->persist($project);
        $this->em->flush();

        // Delete  Unavailables and  et Events attached
        if ($user->getProjects()->isEmpty()) {
            foreach ($user->getUnavailable() as $unavailable) {
                $this->em->remove($unavailable);
            }
            foreach ($user->getEvents() as $event) {
                $this->em->remove($event);
            }
            $this->em->remove($user);
            $this->addFlash('success', 'Le collaborateur a été entièrement supprimé.');
        } else {
            $this->addFlash('success', 'Le collaborateur a été retiré du projet.');
        }

        $this->em->flush();

        return $this->redirectToRoute('project.show', [
            'id' => $project->getId(),
            'slug' => $project->getSlug()
        ]);
    }

}