<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/projets')]
#[IsGranted('ROLE_ADMIN')] 
class ProjectController extends AbstractController

{
    public function __construct(
        
        private ProjectRepository $projectRepo,
        private EntityManagerInterface $em,
        private Security $security
    
    ){}


    #[Route('/', name: 'project.index')]
    public function index(Request $request): Response
    {
        return $this->render('home/index.html.twig');
    }

    /**
     * Retrieve Project by id
     */
    #[Route('/show/{slug}-{id}', name: 'project.show', requirements: ['id' => '\d+', 'slug' => '[A-z0-9-]+'] )]
    public function show(?Project $project): Response
    {

        //  TODO : Faire une Verif if Admin si If son project

        $project = $this->projectRepo->find($project);

        // dd($projects);
        return $this->render('project/_show.html.twig', [
            'project' => $project,
        ]);
    }

    /**
     * New Project 
     */
    #[Route('/nouveau', name: 'project.new')]
    public function newProject(Request $request): Response
    {
        if(!$this->security->getUser()){
            $this->addFlash('danger', "Vous devez être connecté(e) pour accéder à ce service");
            return $this->redirectToRoute('home');
        }
        //  TODO :  Verif si admin comme ds AdminController (à voir faire un service)

        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            if($request->getMethod() === "POST"){

                // Retrieve all data in the form 
                $project = $form->getData();

                // TODO: Prévoir un nombre limiter de caractère
                // Retrieve NameProject
                $slug = str_replace(' ', '', $project->getName());
                $project->setSlug($slug);

                $admin = $this->security->getUser();
                $project->setAdmin($admin);

                $this->em->persist($project);
                $this->em->flush();
            
            // do anything else you need here, like send an email

            $this->addFlash('success', 'Le project a été créer avec succes');

            return $this->redirectToRoute('admin.index');
            }
        }

        return $this->render('project/_new.html.twig', [
            'projectForm' => $form,
        ]);
    }

    /**
     * Edit Project 
     */
    #[Route('/edit/{slug}-{id}', name: 'project.edit', requirements: ['id' => '\d+', 'slug' => '[A-z0-9-]+'] )]
    public function editProject(?Project $project)
    {

        $project = $this->projectRepo->find($project);


        return $this->render('project/_edit.html.twig', [
            'project' => $project,
        ]);
    }


    /**
     * Delete Project 
     */
    #[Route('/supprimer/{id}', name: 'project.delete')]
    public function deleteProject(?Project $project)
    {
        $this->em->remove($project);
        $this->em->flush();
        $this->addFlash('success', 'Le project a été supprimer avec succes');
        return $this->redirectToRoute('admin.index', [], Response::HTTP_SEE_OTHER);
    }

    
}
