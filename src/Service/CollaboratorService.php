<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Project;
use App\DTO\CollaboratorDTO;
use App\Form\CollaboratorType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class CollaboratorService extends AbstractController
{

    public function __construct(

        private CollaboratorDTO $collaboratorDTO,
        private UserRepository $userRepository,
        private UserAuthenticatorInterface $userAuthenticator, 
        private UserAuthenticator $authenticator, 
        private EntityManagerInterface $em,
        private Security $security,
        private UserPasswordHasherInterface $userPasswordHasher

        
        )
    {}

    public function createCollaborator(CollaboratorDTO $collaboratorDTO, Project $project): void
    {
        // Gestion of the Collaborator
        $collaborator = new User();

        // Use of a service => TextFormatterService
        $collaborator->setFirstname(TextFormatterService::formatUcFirst($collaboratorDTO->getFirstname()));
        $collaborator->setLastname(TextFormatterService::formatUcFirst($collaboratorDTO->getLastname()));
        
        $collaborator->setEmail($collaboratorDTO->getEmail());
        $collaborator->setRoles(['ROLE_USER', 'ROLE_COLLABORATOR']);


        $collaborator->addProject($project);
        $project->addCollaborator($collaborator);
        $project->setAdmin($this->security->getUser());

        $password = $collaboratorDTO->getLastname();
        $collaborator->setPassword(
            $this->userPasswordHasher->hashPassword(
                $collaborator,
                $password
            )
        );
        
                $this->em->persist($collaborator);
                $this->em->persist($project);
                $this->em->flush();
    }


    // public function getCollaboratorsByProject(int $projectId): array
    // {
    //     $collaborators = $this->userRepository->findByProjectId($projectId);

    //     $collaboratorDTOs = [];

    //     foreach ($collaborators as $collaborator) {
    //         $collaboratorDTO = new CollaboratorDTO();
    //         $collaboratorDTO->setId($collaborator->getId());
    //         $collaboratorDTO->setFirstname($collaborator->getFirstname());
    //         $collaboratorDTO->setLastname($collaborator->getLastname());
    //         $collaboratorDTO->setEmail($collaborator->getEmail());

    //         $collaboratorDTOs[] = $collaboratorDTO;
    //     }

    //     return $collaboratorDTOs;
    // }



    

}
