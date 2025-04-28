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
    public function __construct(
        private UserRepository $userRepo,
        private EntityManagerInterface $em,
        private ProjectRepository $projectRepo,
        private CheckService $checkService
    ) {}

    #[Route('/espaceperso', name: 'admin.index')]
    public function index(): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('danger', "Vous devez être connecté(e) pour accéder à ce service");
            return $this->redirectToRoute('home');
        }
        if (!$this->checkService->checkAdminAccess()) {
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
        return $this->render('admin/index.html.twig', $templateData);
    }
}

