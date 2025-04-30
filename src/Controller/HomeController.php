<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/')]
class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ProjectRepository $projectRepo, UserRepository $userRepo, EventRepository $eventRepo): Response
    {
        $templateData['counts'] = [
                "projectsCount" => $projectRepo->count(),
                "collaboratorsCount" => $userRepo->count(),
                "eventsCount" => $eventRepo->count()
            ];
        return $this->render('home/index.html.twig', $templateData,
        );
    }
}
