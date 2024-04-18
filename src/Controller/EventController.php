<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use App\Repository\ProjectRepository;
use App\Repository\TypeEventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EventController extends AbstractController
{
    public function __construct(
        
        private EventRepository $eventRepo,
        private EntityManagerInterface $em,
        private Security $security,
    
    ){}
    #[Route('/event/nouveau/{id}', name: 'event.new')]
    public function new(Request $request, int $id, ProjectRepository $projectRepo)
    {

        //TODO: Verif si connected + admin + project existe
        // Mettre form-control sur EventType + add constraints for the entity Event

        $event = new Event();
        $projectId = $projectRepo->find($id);

        $event->setProject($projectId);

        $form = $this->createForm(EventType::class, $event, [
            'projectId' => $id,
        ]);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $eventData = $form->getData();
        
            
            $this->em->persist($eventData);
            $this->em->flush();
            $this->addFlash('success', 'L\'évènement a été créé avec succes');

            return $this->redirectToRoute('calendar', ['id' => $event->getProject()->getId()]);
            // dd($eventData);
        }
        
        return $this->render('event/create.html.twig', [
            'form' => $form,
        ]);
    }
}
