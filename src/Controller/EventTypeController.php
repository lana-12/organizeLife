<?php

namespace App\Controller;

use App\Entity\TypeEvent;
use App\Repository\TypeEventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EventTypeController extends AbstractController
{
    #[Route('/event/type', name: 'event.type')]
    public function index(Request $request, TypeEventRepository $typeEventRepo, EntityManagerInterface $em): Response
    {

    //     $type = new TypeEvent();
    //     $type->setName('Vacances');
    //     $em->persist($type);
    //     $em->flush();
    // $this->addFlash('success', 'Le Type a été créé avec succes');

    return $this->render('event_type/index.html.twig', [
            'controller_name' => 'EventTypeController',
        ]);
    }
}
