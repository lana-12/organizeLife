<?php

namespace App\Controller;

use App\Entity\Project;
use App\Service\EventService;
use App\Repository\EventRepository;
use App\Repository\ProjectRepository;
use Symfony\Bundle\SecurityBundle\Security;
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

        
        ){}
    
    #[Route('/calendar/{id}', name: 'calendar')]
    public function index(?project $project): Response
    {
        // /**
        //  * @var $user
        //  */
        // $user = $this->security->getUser();

        // dd($user);


        return $this->render('calendar/index.html.twig', [
            'project' => $project,
        ]);
    }


    #[Route('/load-events/{id}', name: 'load.events', methods: ['GET', 'POST']  )]
    public function loadEvents(ProjectRepository $projectRepo, int $id): JsonResponse
    {
        // Retrieve all Events
        // $events = $this->eventService->getEvents(); 

        /**
         * @var $user
         */
        $user = $this->security->getUser();
        $project = $projectRepo->find($id);
        // dd($project);

        
        if($user->getId() === $project->getAdmin()->getId()){
            
            $projectId = $project->getId();  
        
            $events = $this->eventService->getEventsForProject($projectId);
     
            return $this->json([
                'formatEvent' => $events,
            ]);        
        
        }
        
    }

  
}
