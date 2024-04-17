<?php

namespace App\EventSubscriber;

use CalendarBundle\CalendarEvents;
use App\Repository\EventRepository;
use CalendarBundle\Entity\Event as CalendarEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use CalendarBundle\Event\CalendarEvent as CalendarBundleEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CalendarSubscriber implements EventSubscriberInterface
{
    private $eventRepository;
    private $router;

    public function __construct(
        EventRepository $eventRepository,
        UrlGeneratorInterface $router
    ) {
        $this->eventRepository = $eventRepository;
        $this->router = $router;
    }

    public static function getSubscribedEvents()
    {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
            'loadEvents' => 'onCalendarLoadEvents',
        ];
    }
    

    public function onCalendarLoadEvents(CalendarEvent $calendar)
    {
        dump($calendar);
        $start = $calendar->getStart();
        $end = $calendar->getEnd();
    
        $events = $this->eventRepository
            ->createQueryBuilder('event')
            ->where('event.date_event BETWEEN :start and :end')
            ->setParameter('start', $start->format('Y-m-d'))
            ->setParameter('end', $end->format('Y-m-d'))
            ->getQuery()
            ->getResult();
    
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

    
//    // Créez une réponse JSON
   $response = new JsonResponse($formattedEvents);

   // Retournez la réponse
//    $calendar->addData($formattedEvents);    
}
    
}
