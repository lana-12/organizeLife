<?php

namespace App\Controller;

use App\Form\SetPasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    private UserRepository $userRepo;
    private EntityManagerInterface $em;


    public function __construct(
            UserRepository $userRepo,
            EntityManagerInterface $em,
        ) {
            $this->userRepo = $userRepo;
            $this->em = $em;
        }

    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
        ]);
    }


    #[Route('/set-password', name: 'set_password')]
    public function setPassword(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if (!$user || !$user->isFromGoogle()) {
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(SetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword($user, $form->get('plainPassword')->getData());
            $user->setPassword($hashedPassword);
            $user->setIsFromGoogle(false);
            $this->em->flush();
            $this->addFlash('warning', 'Votre mot de passe a bien été défini.');
            return $this->redirectToRoute('home');
        }

        return $this->render('security/setpassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}
