<?php

namespace App\Controller;

use App\Entity\Project;
use App\Service\EventService;
use App\Repository\EventRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use App\Service\CollaboratorService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CalendarController extends AbstractController
{
    public function __construct(
        private EventRepository $eventRepository,
        private EventService $eventService,
        private Security $security,
        ){}
    
    #[Route('/calendar/{id}', name: 'calendar.index')]
    public function index(int $id, Project $project, UserRepository $userRepo, ProjectRepository $projectRepo, EventRepository $eventRepo, CollaboratorService $collaboratorService): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('danger', "Vous devez être connecté(e) pour accéder à ce service");
            return $this->redirectToRoute('home');
        }
        $project = $projectRepo->find($id);
        if(!$project){
            $this->addFlash('danger', "Ce projet n'existe pas");
            return $this->redirectToRoute('admin.index');
        }
        $collaborators = $userRepo->findCollaboratorsByProject($project);
        $totalEvents = $eventRepo->countByProject($project->getId());
        $currentMonthEvents = $eventRepo->countCurrentMonthByProject($project->getId());
        $colorMap = $collaboratorService->generateCollaboratorsColorMap($collaborators);

        $collaboratorsData = [];
        foreach ($collaborators as $collab) {
            $collaboratorsData[] = [
                'id' => $collab->getId(),
                'lastName' => $collab->getLastName(),
                'firstName' => $collab->getFirstName(),
            ];
        }         
        return $this->render('calendar/index.html.twig', [
            'project' => $project,
            'countCollaborator' => count($collaborators),
            'totalEvents' => $totalEvents,
            'currentMonthEvents' => $currentMonthEvents,
            'collaborators' => $collaborators,
            'collaborators_json' => json_encode($collaboratorsData),
            'colorMap' => $colorMap,
        ]);
    }

    #[Route('/load-events/{id}', name: 'load.events', methods: ['GET', 'POST']  )]
    public function loadEvents(int $id, ProjectRepository $projectRepo)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $project = $projectRepo->find($id);
        if (!$project) {
                return $this->json(['error' => 'Projet introuvable'], 404);
            }

                $start = microtime(true);


        if($user->getId() === $project->getAdmin()->getId()){
            $projectId = $project->getId();  
            $events = $this->eventService->getEventsForProject($projectId);
            dump($events);
            $duration = microtime(true) - $start;

dump("Temps de génération des events : " . $duration . " secondes");
            return $this->json([
                'formatEvent' => $events,
            ]);        
        }
           return $this->json(['error' => 'Accès non autorisé'], 403);      

    }
  
}
