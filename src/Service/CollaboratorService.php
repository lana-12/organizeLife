<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Project;
use App\DTO\CollaboratorDTO;
use App\Form\CollaboratorType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use App\Service\TextFormatterService;
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
        ){}

    public function createCollaborator(CollaboratorDTO $collaboratorDTO, Project $project): void
    {
        // Gestion of the Collaborator
        $collaborator = new User();

        // Use of a service => TextFormatterService
        $collaborator->setFirstname(TextFormatterService::formatUcFirst($collaboratorDTO->getFirstname()));
        $collaborator->setLastname(TextFormatterService::formatUcFirst($collaboratorDTO->getLastname()));
        $collaborator->setEmail($collaboratorDTO->getEmail());
        $collaborator->setRoles(['ROLE_COLLABORATOR']);

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


    public function buildCollaboratorDTOFromUser(User $user, Project $project): CollaboratorDTO
    {
        return new CollaboratorDTO(
            $user->getFirstname(),
            $user->getLastname(),
            $user->getEmail(),
            $user,
            $project
        );
    }


    public function generateCollaboratorsColorMap(array $collaborators): array
    {
        $colorMap = [];

        foreach ($collaborators as $collaborator) {
            $bgColor = $this->generateColorFromId($collaborator->getId());
            $textColor = $this->getContrastingTextColor($bgColor);
            $colorMap[$collaborator->getId()] = [
                'background' => $bgColor,
                'text' => $textColor
            ];
        }

        return $colorMap;
    }

    private function generateColorFromId(int $id): string
    {
        // Generete color 
        $hue = ($id * 47) % 360; 
        return "hsl($hue, 70%, 70%)";
    }

    private function getContrastingTextColor(string $hslColor): string
    {
        // Convert HSL en RGB of calcul light 
        if (!preg_match('/hsl\((\d+),\s*(\d+)%,\s*(\d+)%\)/', $hslColor, $matches)) {
            return '#000'; 
        }
        [$h, $s, $l] = [(int)$matches[1], (int)$matches[2] / 100, (int)$matches[3] / 100];
        // HSL to RGB conversion
        $c = (1 - abs(2 * $l - 1)) * $s;
        $x = $c * (1 - abs(fmod($h / 60.0, 2) - 1));
        $m = $l - $c / 2;
        [$r, $g, $b] = match (true) {
            $h < 60  => [$c, $x, 0],
            $h < 120 => [$x, $c, 0],
            $h < 180 => [0, $c, $x],
            $h < 240 => [0, $x, $c],
            $h < 300 => [$x, 0, $c],
            default  => [$c, 0, $x],
        };
        $r = ($r + $m);
        $g = ($g + $m);
        $b = ($b + $m);
        $luminance = 0.299 * $r + 0.587 * $g + 0.114 * $b;
        return $luminance > 0.6 ? '#000000' : '#ffffff';
    }

}