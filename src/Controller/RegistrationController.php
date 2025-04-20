<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\UserAuthenticator;
use App\Service\TextFormatterService;
use Symfony\Component\Form\FormError;
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
        $form = $this->createForm(RegistrationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData(); 

            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $data->getEmail()]);
            if ($existingUser) {
                $form->get('email')->addError(new FormError('Cet email est déjà utilisé.'));
            } else {
                $user = new User();
                $user->setFirstname(TextFormatterService::formatUcFirst($data->getFirstname()));
                $user->setLastname(TextFormatterService::formatUcFirst($data->getLastname()));
                $user->setEmail($data->getEmail());
                $user->setPassword(
                    $userPasswordHasher->hashPassword($user, $form->get('plainPassword')->getData())
                );
                $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);

                $entityManager->persist($user);
                $entityManager->flush();

                // Authentifier l'utilisateur
                return $userAuthenticator->authenticateUser($user, $authenticator, $request);
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    
    
}
