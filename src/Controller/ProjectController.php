<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/projets')]
class ProjectController extends AbstractController


{
    public function __construct(
        
        private ProjectRepository $projectRepo
    
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
    #[Route('/show/{slug}-{id}', name: 'project.show', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+'] )]
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
    public function new(Request $request, Security $security): Response
    {
        if(!$security->getUser()){
            $this->addFlash('danger', "Vous devez être connecté(e) pour accéder à ce service");
            return $this->redirectToRoute('home');
        }


        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);


        
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $project = $form->getData();
            $slug = $project->getSlug();
            dd($slug);
            

            if($request->getMethod() === "POST"){


                $admin = $security->getUser();
                $project->setAdmin($admin);

                if ($form->getConfig()->getData() === "") {
                    // $project->setSlug()
                }




        
            //     $entityManager->persist($user);
            //     $entityManager->flush();
            //     // do anything else you need here, like send an email
    
            //     return $userAuthenticator->authenticateUser(
            //         $user,
            //         $authenticator,
            //         $request
            //     );
            }
        }

        return $this->render('project/new.html.twig', [
            'projectForm' => $form,
        ]);
    }

    
}
