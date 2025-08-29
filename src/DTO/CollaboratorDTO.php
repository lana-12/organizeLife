<?php

namespace App\DTO;

use App\Entity\User;
use App\Entity\Project;

class CollaboratorDTO
{
    public function __construct(
        
        private ?string $firstname = null,
        private ?string $lastname = null,
        private ?string $email = null,
        private ?User $user = null,
        private ?Project $project = null,
    
    ){}

    // Getters and setters
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set the value of firstname
     *
     * @return  self
     */ 
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get the value of lastname
     */ 
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set the value of lastname
     *
     * @return  self
     */ 
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get the value of email
     */ 
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */ 
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

        /**
         * Get the value of user
         */ 
        public function getUser()
        {
                return $this->user;
        }

        /**
         * Set the value of user
         *
         * @return  self
         */ 
        public function setUser($user)
        {
                $this->user = $user;

                return $this;
        }

        /**
         * Get the value of project
         */ 
        public function getProject()
        {
                return $this->project;
        }

        /**
         * Set the value of project
         *
         * @return  self
         */ 
        public function setProject($project)
        {
                $this->project = $project;

                return $this;
        }
}