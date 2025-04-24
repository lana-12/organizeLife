<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserEditType;
use App\Service\CheckService;
use App\Form\PasswordChangeType;
use App\Repository\UserRepository;
use App\Service\TextFormatterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/user')]
#[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_COLLABORATOR")'))]
class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserRepository $userRepo,
        private CheckService $checkService
    ) {}

    #[Route('/edit/{id<\d+>}', name: 'user.edit')]
    public function edit(int $id, Request $request):Response
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
        $userToEdit = $this->userRepo->find($id); 
        if (!$userToEdit) {
            $this->addFlash('danger', "Utilisateur introuvable");
            return $this->redirectToRoute('home'); 
        }

        $form = $this->createForm(UserEditType::class, $userToEdit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userToEdit->setFirstname(TextFormatterService::formatUcFirst($form->getData()->getFirstname()));
            $userToEdit->setLastname(TextFormatterService::formatUcFirst($form->getData()->getLastname()));

            $this->em->flush();
            $this->addFlash('success', 'Vos données ont été mis à jour !');
            return $this->redirectToRoute('admin.index', ['id' => $userToEdit->getId()]);
        }
        return $this->render('user/edit.html.twig', [
            'titlePage'=>'Modification de mes données',
            'formEditUser' => $form->createView(),
        ]);
    }

    #[Route('/edit-password/{id<\d+>}', name: 'user.edit.password')]
    public function changePassword(int $id, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('danger', "Vous devez être connecté(e) pour accéder à ce service");
            return $this->redirectToRoute('home');
        }

        $userToEdit = $this->userRepo->find($id);
        if (!$userToEdit) {
            $this->addFlash('danger', "Utilisateur introuvable");
            return $this->redirectToRoute('home');
        }
        $form = $this->createForm(PasswordChangeType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentPassword = $form->get('currentPassword')->getData();
            $newPassword = $form->get('newPassword')->getData();

            if (!$passwordHasher->isPasswordValid($userToEdit, $currentPassword)) {
                $this->addFlash('danger', "Le mot de passe actuel est incorrect");
                return $this->redirectToRoute('user.change_password', ['id' => $userToEdit->getId()]);
            }
            $hashedPassword = $passwordHasher->hashPassword($userToEdit, $newPassword);
            $userToEdit->setPassword($hashedPassword);
            $this->em->flush();
            $this->addFlash('success', "Le mot de passe a été mis à jour avec succès");
            return $this->redirectToRoute('admin.index', ['id' => $userToEdit->getId()]);
        }

        return $this->render('user/change_password.html.twig', [
            'titlePage' => 'Changer le mot de passe',
            'formChangePwd' => $form->createView(),
        ]);
    }

}