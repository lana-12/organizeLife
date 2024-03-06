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
    #[Route('/', name: 'project.index')]
    public function index(Request $request, ProjectRepository $projectRepo): Response
    {
        $projects = $projectRepo->findAll();
        
        
        // dd($projects);
        return $this->render('project/index.html.twig', [
            'projects' => $projects,
            
        ]);
    }

    #[Route('/show/{slug}-{id}', name: 'project.show', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+'])]
    public function show(Request $request, ProjectRepository $projectRepo, int $id, string $slug): Response
    {
        $project = $projectRepo->find($id);
        dump($project);

        // dd($projects);
        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }
}
