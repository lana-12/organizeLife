<?php

namespace App\Controller;

use App\Entity\TypeEvent;
use App\Form\TypeEventType;
use App\Service\CheckService;
use App\Repository\EventRepository;
use App\Repository\ProjectRepository;
use App\Service\TextFormatterService;
use App\Repository\TypeEventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/typeEvents')]

class EventTypeController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private TextFormatterService $textFormatter,
        private CheckService $checkService,
        private Security $security,
        private TypeEventRepository $typeEventRepository,
        private ProjectRepository $projectRepo,
    ){}

    #[Route('/', name: 'event.type')]
    public function index(): Response
    {
        $this->addFlash('danger', "Cette page n'existe pas");
        return $this->redirectToRoute('home');
    }

    #[Route('/nouveau/{id}', name: 'event.type.new')]
    public function new(int $id, Request $request): Response
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

        $project= $this->projectRepo->find($id);
        if (!$project) {
            $this->addFlash('danger', 'Le projet est introuvable.');
            return $this->redirectToRoute('admin.index');
        }

        $type = new TypeEvent();
        $typeEventsList = $this->typeEventRepository->findByUser($user);

        $form = $this->createForm(TypeEventType::class, $type);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $type = $form->getData();
            $type->setName($this->textFormatter->formatUcFirst($type->getName()));
            $type->setUser($user);

            $this->em->persist($type);
            $this->em->flush();
            $this->addFlash('success', 'Le Type a été créé avec succes');
            return $this->redirectToRoute('project.show', ['id'=>$project->getId(), 'slug'=>$project->getSlug() ]);
        }

        return $this->render('event_type/_formEventType.html.twig', [
                'typeEventForm' => $form,
                'typeEventsList' => $typeEventsList,
                'typeEventsCount' => count($typeEventsList),
                'editMode'=> $type->getId() !== null,
                'project'=> $project, 
            ]);
        }


    #[Route('/edit/{id}/{project}', name: 'event.type.edit')]
    public function edit(int $id, int $project,  Request $request): Response
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

        $project= $this->projectRepo->find($project);
        if (!$project) {
            $this->addFlash('danger', 'Le projet est introuvable.');
            return $this->redirectToRoute('admin.index');
        }

        $type = $this->typeEventRepository->find($id);
        if(!$type){
            $this->addFlash('danger', "Ce type d'évènement n'existe pas");
            return $this->redirectToRoute('admin.index');
        }
        $typeEventsList = $this->typeEventRepository->findByUser($user);

        $form = $this->createForm(TypeEventType::class, $type);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $type = $form->getData();
            $type->setName($this->textFormatter->formatUcFirst($type->getName()));
            $type->setUser($user);

            $this->em->persist($type);
            $this->em->flush();
            $this->addFlash('success', 'Le Type a été créé avec succes');
            return $this->redirectToRoute('project.show', ['id'=>$project->getId(), 'slug'=>$project->getSlug() ]);
        }
        return $this->render('event_type/_formEventType.html.twig', [
                'typeEventForm' => $form,
                'typeEventsList' => $typeEventsList,
                'typeEventsCount' => count($typeEventsList),
                'editMode'=> $type->getId() !== null,
                'project'=> $project,
                
            ]);
    }


    #[Route('/delete/{id}/{project}', name: 'event.type.delete')]
    public function delete(int $id, int $project, Request $request): Response
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

        $project= $this->projectRepo->find($project);
        if (!$project) {
            $this->addFlash('danger', 'Le projet est introuvable.');
            return $this->redirectToRoute('admin.index');
        }

        $type = $this->typeEventRepository->find($id);
        if (!$type) {
            $this->addFlash('danger', "Ce type d'évènement n'existe pas");
            return $this->redirectToRoute('event.type');
        }

        if (count($type->getEvents()) > 0) {
            $this->addFlash('danger', "Impossible de supprimer ce type car des événements y sont liés.");
            return $this->redirectToRoute('project.show', ['id'=>$project->getId(), 'slug'=>$project->getSlug() ]);

        }

        $this->em->remove($type);
        $this->em->flush();
        $this->addFlash('success', "Le type d'évènement a été supprimé avec succès.");
        return $this->redirectToRoute('project.show', ['id'=>$project->getId(), 'slug'=>$project->getSlug() ]);
    }

}
