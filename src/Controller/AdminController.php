<?php

namespace App\Controller;

use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
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
        private ProjectRepository $projectRepo

    
    ){}


    #[Route('/', name: 'admin.index')]
    public function index(): Response
    {

        if(!$this->security->getUser()){
            $this->addFlash('danger', "Vous devez être connecté(e) pour accéder à ce service");
            return $this->redirectToRoute('home');
        }

        /**
         * @var User 
         */
        $adminProject = $this->security->getUser();
        $adminId = $adminProject->getId();

        
        $projects = $this->projectRepo->findByProjectsByAdminId($adminId);
        
        // dd($adminProject);
        // dd($projects);


        return $this->render('admin/index.html.twig', [
            'admin' => $adminProject,
            'projects' => $projects,
        ]);
    }
}
