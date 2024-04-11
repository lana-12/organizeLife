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


    /**
     * Retrieve AllProjects
     *
     * @return Response
     */
    #[Route('/', name: 'project.index')]
    public function index(Request $request): Response
    {
        $projects = $this->projectRepo->findAll();

        // dd($projects);
        return $this->render('project/index.html.twig', [
            'projects' => $projects,
            
        ]);
    }

    /**
     * Retrieve Project by id
     */
    #[Route('/show/{slug}-{id}', name: 'project.show', requirements: ['id' => '\d+', 'slug' => '[A-z0-9-]+'] )]
    public function show(int $id, string $slug, Request $request): Response
    {
        $project = $this->projectRepo->find($id);

        // dd($projects);
        return $this->render('project/show.html.twig', [
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

        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            if($request->getMethod() === "POST"){

                // Retrieve all data in the form 
                $project = $form->getData();

                // TODO: Prévoir un nombre limiter de caractère
                // Retrieve NameProject
                $slug = $project->getName();
                $project->setSlug($slug);

                $admin = $this->security->getUser();
                $project->setAdmin($admin);

                $this->em->persist($project);
                $this->em->flush();
            
            // do anything else you need here, like send an email

            $this->addFlash('success', 'Le project a été créer avec succes');

            return $this->redirectToRoute('project.index');
            }
        }

        return $this->render('project/new.html.twig', [
            'projectForm' => $form,
        ]);
    }

    /**
     * New Project 
     */
    #[Route('/supprimer/{id}', name: 'project.delete')]
    public function deleteProject(?Project $project)
    {
        $this->em->remove($project);
        $this->em->flush();
        $this->addFlash('success', 'Le project a été supprimer avec succes');
        return $this->redirectToRoute('project.index', [], Response::HTTP_SEE_OTHER);
    }
    
}
