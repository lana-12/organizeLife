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

        //TODO: Verif si connected + admin + project(ok) existe  => OK
        // Mettre form-control sur EventType + add constraints on the entity Event
        // Faire un service pour les checks

        /**
         * @var User 
         */
        $user = $this->security->getUser();

        // Check if user is logged in
        if(!$user){
            $this->addFlash('danger', "Vous devez être connecté(e) pour accéder à ce service");
            return $this->redirectToRoute('app_login');
        }

        // Check if user is Admin
        if(!in_array("ROLE_ADMIN", $user->getRoles(), true )){
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

        $startDate = $request->query->get('start');
        $today= date('d-m-Y');
        // dd($today);

        if(strtotime($startDate) < strtotime($today)) {
            $this->addFlash('danger', 'Cette date est déjà passée !!');
            return $this->redirectToRoute('calendar', ['id' => $event->getProject()->getId()]);
        }
        // if ($startDate) {
        //     $event->setDateEvent(new \DateTimeImmutable($startDate));
        // }

        $form = $this->createForm(EventType::class, $event, [
            'projectId' => $id,
            'start_date' => $startDate,
        ]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($request->getMethod() === "POST"){

                $eventData = $form->getData();
            
                
                $this->em->persist($eventData);
                $this->em->flush();
                $this->addFlash('success', 'L\'évènement a été créé avec succes');

                return $this->redirectToRoute('calendar', ['id' => $event->getProject()->getId()]);
                // dd($eventData);
            }
        }
        
        return $this->render('event/create.html.twig', [
            'form' => $form,
        ]);
    }
}
