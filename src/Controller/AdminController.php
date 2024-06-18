<?php

namespace App\Controller;

use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use App\Service\CheckService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
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
        private Security $security,
        private ProjectRepository $projectRepo,
        private CheckService $checkService,
    ){}


    #[Route('/espaceperso', name: 'admin.index')]
    public function index(): Response
    {
        /**
         * @var User 
         */
        $user = $this->security->getUser();

        // Check if user is logged in
        if(!$user){
            $this->addFlash('danger', "Vous devez être connecté(e) pour accéder à ce service");
            return $this->redirectToRoute('home');
        }

        // Check if user is Admin (CheckService)
        if(!CheckService::checkAdminAccess($user)){
            $this->addFlash('danger', "Vous ne disposez pas des droits pour accéder à ce service");
            return $this->redirectToRoute('home');
        } 


        $adminId = $user->getId();
        $projects = $this->projectRepo->findByProjectsByAdminId($adminId);
        $counts = $this->projectRepo->getAdminStatistics($adminId);
// dd($counts);

        if ($projects) {
            return $this->render('admin/index.html.twig', [
                'admin' => $user,
                'projects' => $projects,
                'role' => 'Admin',
                'counts' => $counts,
            ]);
        } else {
            $this->addFlash('danger', "Vous n'avez aucun projet");
            return $this->render('admin/index.html.twig', [
                'user' => $user,
                'role'=> 'User'
            ]);
        }
    }
}
