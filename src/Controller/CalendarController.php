<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CalendarController extends AbstractController
{
    
    public function __construct(
        private EventRepository $eventRepository)

    {}

    
    #[Route('/calendar', name: 'app_calendar')]
    public function index(): Response
    {
        return $this->render('calendar/index.html.twig', [
            'controller_name' => 'CalendarController',
        ]);
    }




    #[Route('/load-events', name: 'load.events',methods: ['GET', 'POST']  )]
    public function loadEvents(): JsonResponse
    {

        $events = $this->eventRepository->findAll(); 
        
        dd($events);

        $formattedEvents = [];
        foreach ($events as $event) {
            $formattedEvents[] = [
                'title' => $event->getTitle(),
                'date_event' => $event->getDateEvent()->format('Y-m-d'),
                'hour_event' => $event->getHourEvent()->format('H:i:s'),
                'description' => $event->getDescription(),
                // Ajoutez d'autres propriétés si nécessaire
            ];
        }

        return $this->json([
            'formatEvent' => $formattedEvents ,
            
        ]);    
    }
}
