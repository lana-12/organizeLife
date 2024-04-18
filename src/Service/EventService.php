<?php

namespace App\Service;

use App\Repository\EventRepository;

class EventService 
{

    public function __construct(
        private EventRepository $eventRepository,

        ){}
    

    public function getEventsForProject($projectId): array
    {
        $events = $this->eventRepository->findBy(['project' => $projectId]); 
        $formattedEvents = [];
        foreach ($events as $event) {
            $formattedEvents[] = [
                'title' => $event->getTitle(),
                'date_event' => $event->getDateEvent()->format('Y-m-d'),
                'hour_event' => $event->getHourEvent()->format('H:i:s'),
                'description' => $event->getDescription(),
                'project' => [
                    'id' => $event->getProject()->getId(),
                    'name' => $event->getProject()->getName(),  
                ],
            ];
        }

        return $formattedEvents;
    }


    /**
     * Retrieve allEvent
    */
    public function getEvents(): array
    {
        $events = $this->eventRepository->findAll(); 
        
        $formattedEvents = [];
        foreach ($events as $event) {
            $formattedEvents[] = [
                'title' => $event->getTitle(),
                'date_event' => $event->getDateEvent()->format('Y-m-d'),
                'hour_event' => $event->getHourEvent()->format('H:i:s'),
                'description' => $event->getDescription(),
            ];
        }
        return $formattedEvents;    
    }


}
