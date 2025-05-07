<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\EventRepository;

class EventService 
{

    public function __construct(
        private EventRepository $eventRepository,
        ){}
    
    public function getEventsForProject($projectId): array
    {
        $events = $this->eventRepository->findWithRelationsByProject($projectId);
        $formattedEvents = [];

        foreach ($events as $event) {
            $user = $event->getUser();
            $formattedEvents[] = [
                'id_event'=> $event->getId() ?? '',
                'collaborateur_id'=> $user->getId() ?? '',
                'title' => $event->getTitle(),
                'date_event_start' => $event->getDateEventStart()->format('Y-m-d'),
                'hour_event_start' => $event->getHourEventStart()->format('H:i:s'),
                'date_event_end' => $event->getDateEventEnd()->format('Y-m-d'),
                'hour_event_end' => $event->getHourEventEnd()->format('H:i:s'),
                'description' => $event->getDescription() ?? 'Aucune description',
                'type' => $event->getTypeEvent()?->getName() ?? 'Aucun type d\'évènement',
                'collaboratorName'=> $user->getLastName() ?? 'Aucun Collaborateur',
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
                'date_event' => $event->getDateEventStart()->format('Y-m-d'),
                'hour_event' => $event->getHourEventStart()->format('H:i:s'),
                'description' => $event->getDescription(),
            ];
        }
        return $formattedEvents;    
    }


}
