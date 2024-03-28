<?php

namespace App\Controller;

use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Request;
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
    #[Route('/show/{slug}-{id}', name: 'project.show', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+'])]
    public function show(int $id, string $slug, Request $request): Response
    {
        $project = $this->projectRepo->find($id);

        

        // dd($projects);
        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }
}
