<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\ProjectRepository;
use App\Repository\TypeEventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EventController extends AbstractController
{
    #[Route('/event', name: 'event')]
    public function index(EntityManagerInterface $em, ProjectRepository $projectRepository, TypeEventRepository $typeEventRepo): Response
    {
        $idProject = $projectRepository->find(2);
        $idType= $typeEventRepo->find(1);
        $event = new Event();
        $event->setTitle('Midi');
        $event->setTypeEvent($idType);
        $event->setProject($idProject);

        // Date and hour for event
        $event->setDateEvent(new \DateTimeImmutable('2024-04-22'));
        $event->setHourEvent(new \DateTimeImmutable('12:00:00'));

        $event->setDescription("Allez le chercher à midi ");

        $em->persist($event);
        $em->flush();
        $this->addFlash('success', 'L event a été créé avec succes');


        return $this->render('event/index.html.twig', [
            'controller_name' => 'EventController',
        ]);
    }
}
