<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\UserAuthenticator;
use App\Service\TextFormatterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return new JsonResponse(['success' => false, 'message' => 'Données invalides'], Response::HTTP_BAD_REQUEST);
        }
        try {
            $user = new User();
            $user->setFirstname(TextFormatterService::formatUcFirst($data['firstname']));
            $user->setLastname(TextFormatterService::formatUcFirst($data['lastname']));
            $user->setEmail($data['email']);
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user, 
                    $data['password']));
            $user->setRoles(
                ['ROLE_USER', 'ROLE_ADMIN']);
            $entityManager->persist($user);
            $entityManager->flush();

            // Authentifier l'utilisateur après inscription
            $userAuthenticator->authenticateUser($user, $authenticator, $request);

            return new JsonResponse(['success' => true, 'message' => 'Inscription réussie !'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'Erreur lors de l\'inscription'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }  
    }


}
