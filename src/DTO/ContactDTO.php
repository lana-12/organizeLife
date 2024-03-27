<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ContactDTO 

{
    #[Assert\NotBlank()]
    #[Assert\Length(min:3, max: 200)]
    public string $name = '';

    #[Assert\NotBlank()]
    #[Assert\Email()]
    public string $email = '';

    #[Assert\NotBlank()]
    #[Assert\Length(min: 3, max: 100)]
    public string $subject = '';

    #[Assert\NotBlank()]
    #[Assert\Length(min: 3, max: 500)]
    public string $message = '';

    #[Assert\NotBlank()]
    public array $collaborators = [];


    // Retrieve allUserEmail
    public function getCollaboratorsEmails(): array
    {
        return $this->collaborators;
    }


    

}