<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Project;
use App\DTO\CollaboratorDTO;
use App\Service\CheckService;
use App\Form\CollaboratorType;
use App\Service\MailerService;
use App\Service\CollaboratorService;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/collaborators')]
#[IsGranted('ROLE_ADMIN')] 
class CollaboratorController extends AbstractController
{
    public function __construct(
        private CollaboratorService $collaboratorService,
        private ProjectRepository $projectRepo,
        private EntityManagerInterface $em,
        private Security $security,
        private MailerService $mailer
    ){}

    #[Route('/ajouter/{id}', name: 'collaborator.new', requirements: ['id' => '\d+', ])]
    public function new(Request $request, int $id, CheckService $checkService): Response
    {

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $admin = $user->getId();

        if(!$this->security->getUser()){
            $this->addFlash('danger', "Vous devez être connecté(e) pour accéder à ce service");
            return $this->redirectToRoute('home');
        }

        // Check if user is Admin (CheckService)
        if(!$checkService->checkAdminAccess()){
            $this->addFlash('danger', "Vous ne disposez pas des droits pour accéder à ce service");
            return $this->redirectToRoute('home');
        } 
        
        // Retrieve project by id paramater url
        $project = $this->projectRepo->find($id);

        // Gestion of the CollaboratorDTO
        $collaboratorDTO = new CollaboratorDTO();
        $form = $this->createForm(CollaboratorType::class, $collaboratorDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->collaboratorService->createCollaborator($collaboratorDTO, $project);
            $this->mailer->sendEmail(
                    $project->getAdmin()->getEmail(),
                    $data->getEmail(),
                    'Bienvenue sur ' . $project->getName(),
                    'emails/newCollaborator.html.twig',
                    [
                        'project' => $project,
                        'collaborator' =>$data
                    ]
                );
            $this->addFlash('success', 'Collaborateur créé avec succès !!');
            return $this->redirectToRoute('project.show', [
                'id' => $project->getId(),
                'slug' => $project->getSlug(),
            ]);
        }
        return $this->render('collaborator/formNewCollaborator.html.twig', [
            'collaboratorform' => $form,
            'project' => $project,

        ]);    
    
    }
}
