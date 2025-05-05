<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Service\CheckService;
use App\Repository\EventRepository;
use App\Repository\ProjectRepository;
use App\Repository\TypeEventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route(path: '/event')]
#[IsGranted('ROLE_ADMIN')] 
class EventController extends AbstractController
{
    public function __construct( 
        private EventRepository $eventRepo,
        private EntityManagerInterface $em,
        private Security $security,
        private CheckService $checkService,
    ){}

    #[Route('/nouveau/{id}', name: 'event.new')]
    public function new(int $id, Request $request,  ProjectRepository $projectRepo)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Check if user is logged in
        if(!$user){
            $this->addFlash('danger', "Vous devez être connecté(e) pour accéder à ce service");
            return $this->redirectToRoute('app_login');
        }

        // Check if user is Admin (CheckService)
        if(!$this->checkService->checkAdminAccess()){
            $this->addFlash('danger', "Vous ne disposez pas des droits pour accéder à ce service");
            return $this->redirectToRoute('home');
        } 

        $projectId = $projectRepo->find($id);
        if(!$projectId){
            $this->addFlash('danger', "Ce projet n'existe pas");
            return $this->redirectToRoute('admin.index');
        }
        
        $event = new Event();
        $event->setProject($projectId);
        $startDate="";
        $startEnd = "";
        // Retrieving the querystring for the date selected

        if($request->query->get('start') && $request->query->get('end')) {
            $startDate = $request->query->get('start');
            $startEnd = $request->query->get('end');
            $today= date('d-m-Y');
            if(strtotime($startDate) < strtotime($today)) {
                $this->addFlash('danger', 'Cette date est déjà passée !!');
                return $this->redirectToRoute('calendar', ['id' => $event->getProject()->getId()]);
            }
            
        }
        $form = $this->createForm(EventType::class, $event, [
            'projectId' => $id,
            'start_date' => $startDate,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($request->getMethod() === "POST"){
                $eventData = $form->getData();

                $start = new \DateTime($eventData->getDateEventStart()->format('Y-m-d') . ' ' . $eventData->getHourEventStart()->format('H:i:s'));
                $end = new \DateTime($eventData->getDateEventEnd()->format('Y-m-d') . ' ' . $eventData->getHourEventEnd()->format('H:i:s'));
                if ($end <= $start) {
                    $this->addFlash('danger', 'La date et l\'heure de fin doivent être postérieures à celles de début.');
                    return $this->render('event/_formEvent.html.twig', [
                        'eventForm' => $form,
                        'editMode'=> $event->getId() !== null,
                    ]);
                }
                $this->em->persist($eventData);
                $this->em->flush();
                $this->addFlash('success', 'L\'évènement a été créé avec succes');
                return $this->redirectToRoute('calendar', ['id' => $event->getProject()->getId()]);
            }
        }
        return $this->render('event/_formEvent.html.twig', [
            'eventForm' => $form,
            'editMode'=> $event->getId() !== null,
        ]);
    }

    /**
    * Edit Event 
    */
    #[Route('/edit/{id}', name: 'event.edit', requirements: ['id' => '\d+'] )]
    public function editProject(int $id, Request $request)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $event = $this->eventRepo->find($id);
        if(!$this->security->getUser()){
            $this->addFlash('danger', "Vous devez être connecté(e) pour accéder à ce service");
            return $this->redirectToRoute('home');
        }
        // Check if user is Admin (CheckService)
        if(!$this->checkService->checkAdminAccess()){
            $this->addFlash('danger', "Vous ne disposez pas des droits pour accéder à ce service");
            return $this->redirectToRoute('home');
        } 
        if(!$event){
            $this->addFlash('danger', "Cet évènement n'existe pas");
            return $this->redirectToRoute('admin.index');
        }

        $form = $this->createForm(EventType::class, $event, [
                'projectId' => $event->getProject()->getId(),  
            ]);
        $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()) {
                $event = $form->getData();
                $start = new \DateTime($event->getDateEventStart()->format('Y-m-d') . ' ' . $event->getHourEventStart()->format('H:i:s'));
                $end = new \DateTime($event->getDateEventEnd()->format('Y-m-d') . ' ' . $event->getHourEventEnd()->format('H:i:s'));
                if ($end <= $start) {
                    $this->addFlash('danger', 'La date et l\'heure de fin doivent être postérieures à celles de début.');
                    return $this->render('event/_formEvent.html.twig', [
                        'eventForm' => $form,
                        'editMode'=> $event->getId() !== null,
                    ]);
                }
                $this->em->persist($event);
                $this->em->flush();
            
            // do anything else you need here, like send an email

            $this->addFlash('success', 'L\'évènement a été modifier avec succes');
            return $this->redirectToRoute('calendar', ['id' => $event->getProject()->getId()]); 
            }
        return $this->render('event/_formEvent.html.twig', [
            'eventForm' => $form,
            'editMode'=> $event->getId() !== null,
        ]);
    }

    #[Route('/delete/{id}', name: 'event.delete')]
    public function delete(Request $request, Event $event): Response
    {
        if (!$this->security->getUser()) {
            $this->addFlash('danger', "Vous devez être connecté(e) pour accéder à ce service");
            return $this->redirectToRoute('app_login');
        }

        if (!$this->checkService->checkAdminAccess()) {
            $this->addFlash('danger', "Vous ne disposez pas des droits pour accéder à ce service");
            return $this->redirectToRoute('home');
        }

        if (!$event) {
            $this->addFlash('danger', "L'évènement n'existe pas");
            return $this->redirectToRoute('calendar', ['id' => $event->getProject()?->getId() ?? 0]);
        }

        $projectId = $event->getProject()?->getId() ?? null;

        $this->em->remove($event);
        $this->em->flush();

        $this->addFlash('success', "L'évènement a été supprimé avec succès");

        return $this->redirectToRoute('calendar', ['id' => $projectId]);
    } 
}