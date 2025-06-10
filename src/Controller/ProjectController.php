<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Service\CheckService;
use App\Repository\UserRepository;
use App\Repository\EventRepository;
use App\Repository\ProjectRepository;
use App\Service\TextFormatterService;
use App\Repository\TypeEventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/projets')]
class ProjectController extends AbstractController

{
    public function __construct(
        private ProjectRepository $projectRepo,
        private EntityManagerInterface $em,
        private Security $security,
        private TextFormatterService $textFormatter,
        private CheckService $checkService,
        private EventRepository $eventRepo,
        private TypeEventRepository $typeEventRepository,

    ){}

    #[Route('/', name: 'project.index')]
    public function index(Request $request): Response
    {
        return $this->render('home/index.html.twig');
    }

    /**
     * Retrieve Project by id
     */
    #[Route('/show/{slug}-{id}', name: 'project.show', requirements: ['id' => '\d+','slug' => '[A-zÀ-ú0-9-]+'] )]
    public function show(Project $project, Request $request, UserRepository $userRepo): Response
    {

        /** @var \App\Entity\User $user */
        $user = $this->security->getUser();

        if(!$this->security->getUser()){
            $this->addFlash('danger', "Vous devez être connecté(e) pour accéder à ce service");
            return $this->redirectToRoute('home');
        }

       // Check if user is Admin (CheckService)
       if(!$this->checkService->checkAdminAccess()){
            $this->addFlash('danger', "Vous ne disposez pas des droits pour accéder à ce service");
            return $this->redirectToRoute('home');
        } 

        if(!$project){
            $this->addFlash('danger', "Ce projet n'existe pas");
            return $this->redirectToRoute('admin.index');
        }
        $typeEventsList = $this->typeEventRepository->findByUser($user);

        if($user->getId() === $project->getAdmin()->getId()){
            $collaborators = $userRepo->findCollaboratorsByProject($project);
            $totalCollaborators = $userRepo->countCollaboratorsByProject($project);
            $totalEvents = $this->eventRepo->countByProject($project->getId());

            return $this->render('project/_show.html.twig', [
                'project' => $project,
                'collaborators' => $collaborators,
                'totalCollaborators' => $totalCollaborators,
                'totalEvents' => $totalEvents,
                'typeEventsList' => $typeEventsList,
                'typeEventsCount' => count($typeEventsList),
            ]);
        }

        $this->addFlash('danger', "Vous ne disposez pas des droits pour accéder à ce projet");
        return $this->redirectToRoute('admin.index');
        
    }

    /**
     * New Project 
     */
    #[Route('/nouveau', name: 'project.new')]
    public function newProject(Request $request): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $admin = $user;

        if(!$this->security->getUser()){
            $this->addFlash('danger', "Vous devez être connecté(e) pour accéder à ce service");
            return $this->redirectToRoute('home');
        }

        // Check if user is Admin (CheckService)
        if(!$this->checkService->checkAdminAccess()){
            $this->addFlash('danger', "Vous ne disposez pas des droits pour accéder à ce service");
            return $this->redirectToRoute('home');
        } 

        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            if($request->getMethod() === "POST"){
                $project = $form->getData();
                $project->setAdmin($admin);

                $formattedSlug = $this->textFormatter->formatSlug($project->getName());
                $project->setSlug($formattedSlug);
                $project->setName($this->textFormatter->formatUcFirst($project->getName()));
                $project->setDescription($this->textFormatter->formatDescription($project->getDescription()));

                $this->em->persist($project);
                $this->em->flush();
            
            // do anything else you need here, like send an email

            $this->addFlash('success', 'Le project a été créé avec succes');

            return $this->redirectToRoute('project.show', ['id'=>$project->getId(), 'slug'=>$project->getSlug() ]);
            }
        }

        return $this->render('project/_formProject.html.twig', [
            'projectForm' => $form,
            'editMode'=> $project->getId() !== null,
        ]);
    }

    /**
     * Edit Project 
     */
    #[Route('/edit/{slug}-{id}', name: 'project.edit', requirements: ['id' => '\d+', 'slug' => '[A-zÀ-ú0-9-]+'] )]
    public function editProject(?Project $project, Request $request)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $admin = $user;

        if(!$this->security->getUser()){
            $this->addFlash('danger', "Vous devez être connecté(e) pour accéder à ce service");
            return $this->redirectToRoute('home');
        }

        // Check if user is Admin (CheckService)
        if(!$this->checkService->checkAdminAccess()){
            $this->addFlash('danger', "Vous ne disposez pas des droits pour accéder à ce service");
            return $this->redirectToRoute('home');
        } 

        if(!$project){
            $this->addFlash('danger', "Ce projet n'existe pas");
            return $this->redirectToRoute('admin.index');
        }

        if($admin->getId() === $project->getAdmin()->getId()){
            if(!$project){
                $project = new Project();
            }
            $form = $this->createForm(ProjectType::class, $project);
            $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()) {
                if($request->getMethod() === "POST"){
    
                    $project = $form->getData();
                    $formattedSlug = $this->textFormatter->formatSlug($project->getName());
                    $project->setSlug($formattedSlug);
                    $project->setName($this->textFormatter->formatUcFirst($project->getName()));
                    $this->em->persist($project);
                    $this->em->flush();
                
                // do anything else you need here, like send an email
    
                $this->addFlash('success', 'Le project a été modifié avec succes');
    
            return $this->redirectToRoute('project.show', ['id'=>$project->getId(), 'slug'=>$project->getSlug() ]);
                }
            }
            return $this->render('project/_formProject.html.twig', [
                'project' => $project,
                'projectForm' => $form,
                'editMode'=> $project->getId() !== null,

            ]);
        }   
    }


    /**
     * Delete Project 
     */
    #[Route('/supprimer/{id}', name: 'project.delete', methods: ['GET', 'POST'])]
    public function deleteProject(?Project $project, Request $request)
    {
        if (!$project) {
            $this->addFlash('danger', 'Le projet est introuvable.');
            return $this->redirectToRoute('admin.index');
        }
        // Check if user is Admin (CheckService)
        if(!$this->checkService->checkAdminAccess()){
            $this->addFlash('danger', "Vous ne disposez pas des droits pour accéder à ce service");
            return $this->redirectToRoute('home');
        } 
        if ($request->isMethod('POST') || $request->isMethod('GET') ) {
            // Save collaborator before delete
            $formerCollaborators = $project->getCollaborator()->toArray();
            // Delete events attached 
            foreach ($project->getEvents() as $event) {
                $this->em->remove($event);
            }
            // Delete collaborator attached
            foreach ($formerCollaborators as $collaborator) {
                $project->removeCollaborator($collaborator);
            }
            $this->em->flush(); 

            // Delete User why is not project
            foreach ($formerCollaborators as $collaborator) {
                if ($collaborator->getProjects()->isEmpty()) {
                    foreach ($collaborator->getUnavailable() as $unavailable) {
                        $this->em->remove($unavailable);
                    }
                    foreach ($collaborator->getEvents() as $event) {
                        $this->em->remove($event);
                    }
                    $this->em->remove($collaborator);
                }
            }

            // Delete  project
            $this->em->remove($project);
            $this->em->flush();
            $this->addFlash('success', 'Le projet a été supprimé avec succès.');
            return $this->redirectToRoute('admin.index');
        }
        return $this->redirectToRoute('project.show', [
            'id' => $project->getId(),
            'slug' => $project->getSlug(),
        ]);
    }
 
}