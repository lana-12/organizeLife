<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/legal')]

final class LegalController extends AbstractController
{
 #[Route('/mentions-legales', name: 'legal.mentions.legales')]
    public function mentionsLegales(): Response
    {
        return $this->render('legal/mentions_legales.html.twig');
    }

    #[Route('/politique-de-confidentialite', name: 'legal.politique.confidentialite')]
    public function politiqueConfidentialite(): Response
    {
        return $this->render('legal/politique_confidentialite.html.twig');
    }

    #[Route('/conditions-utilisation', name: 'legal.conditions.utilisation')]
    public function conditionsUtilisation(): Response
    {
        return $this->render('legal/conditions_utilisation.html.twig');
    }
}
