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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EventController extends AbstractController
{
    public function __construct(
        
        private EventRepository $eventRepo,
        private EntityManagerInterface $em,
        private Security $security,
        private CheckService $checkService,

    
    ){}


    #[Route('/event/nouveau/{id}', name: 'event.new')]
    public function new(Request $request, int $id, ProjectRepository $projectRepo)
    {

        //TODO: Verif si connected + admin + project(ok) existe  => OK
        // Mettre form-control sur EventType=>OK + add constraints on the entity Event
        // Faire un service pour les checks (check admin=OK, )

        /**
         * @var User 
         */
        $user = $this->security->getUser();

        // Check if user is logged in
        if(!$user){
            $this->addFlash('danger', "Vous devez être connecté(e) pour accéder à ce service");
            return $this->redirectToRoute('app_login');
        }

        // Check if user is Admin (CheckService)
        if(!CheckService::checkAdminAccess($user)){
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
        // Retrieving the querystring for the date selected
        if($request->query->get('start')) {
            $startDate = $request->query->get('start');
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
