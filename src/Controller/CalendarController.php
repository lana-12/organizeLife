<?php

namespace App\Controller;

use App\Entity\Project;
use App\Service\EventService;
use App\Repository\UserRepository;
use App\Repository\EventRepository;
use App\Service\CollaboratorService;
use App\Repository\ProjectRepository;
use App\Service\DateValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CalendarController extends AbstractController
{
    public function __construct(
        private EventRepository $eventRepository,
        private EventService $eventService,
        private Security $security,
        private DateValidatorService $dateValidator,
        ){}
    
    #[Route('/calendar/{id}', name: 'calendar')]
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

        if($user->getId() === $project->getAdmin()->getId()){
            $projectId = $project->getId();  
            $events = $this->eventService->getEventsForProject($projectId);
            return $this->json([
                'formatEvent' => $events,
            ]);        
        }
           return $this->json(['error' => 'Accès non autorisé'], 403);      
    }


    
    #[Route('/event/update-time/{id}', name: 'event.update_time'
    )]
    public function updateEventTime(int $id, Request $request, EventRepository $eventRepo, EntityManagerInterface $em): JsonResponse
    {
        $event = $eventRepo->find($id);
        if (!$event) {
            return $this->json(['error' => 'Événement non trouvé'], 404);
        }
        
        $data = json_decode($request->getContent(), true);

        $startDate = $data['date_event_start'];
        $startHour = $data['hour_event_start'];
        $endDate = $data['date_event_end'];
        $endHour = $data['hour_event_end'];
        
        if ($this->dateValidator->isStartInPast($startDate, $startHour)) {
            return $this->json(['error' => 'La date de début est déjà passée.'], 400);
        }

        if (!$this->dateValidator->isEndAfterStart($startDate, $startHour, $endDate, $endHour)) {
            return $this->json(['error' => 'La date de fin doit être postérieure à celle de début.'], 400);
        }
        $event->setDateEventStart(new \DateTimeImmutable($startDate));
        $event->setHourEventStart(new \DateTimeImmutable($startHour));
        $event->setDateEventEnd(new \DateTimeImmutable($endDate));
        $event->setHourEventEnd(new \DateTimeImmutable($endHour));
        $em->flush();

        return $this->json(['success' => true]);
    }

}
