<?php

namespace App\Controller;

use App\Service\CheckService;
use App\Repository\UserRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')] 
class AdminController extends AbstractController
{
    private UserRepository $userRepo;
    private EntityManagerInterface $em;
    private ProjectRepository $projectRepo;
    private CheckService $checkService;

    public function __construct(
        UserRepository $userRepo,
        EntityManagerInterface $em,
        ProjectRepository $projectRepo,
        CheckService $checkService
    ) {
        $this->userRepo = $userRepo;
        $this->em = $em;
        $this->projectRepo = $projectRepo;
        $this->checkService = $checkService;
    }

    #[Route('/espaceperso', name: 'admin.index')]
    public function index(): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('danger', "Vous devez être connecté(e) pour accéder à ce service");
            return $this->redirectToRoute('home');
        }
        if (!$this->checkService->checkAdminAccess($user)) {
            $this->addFlash('danger', "Vous ne disposez pas des droits pour accéder à ce service");
            return $this->redirectToRoute('home');
        }
        $adminId = $user->getId();
        $projects = $this->projectRepo->findByProjectsByAdminId($adminId);
        $templateData = [
            'admin' => $user,
            'projects' => $projects,
            'role' => 'Admin',
            'counts' => []
        ];

        if ($projects) {
            $templateData['counts'] = [
                "projectsCount" => $this->projectRepo->countProjectByAdmin($adminId),
                "collaboratorsCount" => $this->projectRepo->countCollaboratorsByAdminId($adminId),
                "eventsCount" => $this->projectRepo->countEventsByAdminId($adminId),
            ];
        } 
        
        // else {
        //     $this->addFlash('danger', "Vous n'avez aucun projet");
        // }

        return $this->render('admin/index.html.twig', $templateData);
    }
}

