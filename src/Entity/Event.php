<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
#[ORM\Table(name: '`events`')]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $date_event_start = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $date_event_end = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE)]
    private ?\DateTimeImmutable $hour_event_start = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE)]
    private ?\DateTimeImmutable $hour_event_end = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    private ?Project $project = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    private ?TypeEvent $typeEvent = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    private ?User $user = null;

  

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDateEventStart(): ?\DateTimeImmutable
    {
        return $this->date_event_start;
    }

    public function setDateEventStart(\DateTimeImmutable $date_event_start): static
    {
        $this->date_event_start = $date_event_start;

        return $this;
    }

    public function getDateEventEnd(): ?\DateTimeImmutable
    {
        return $this->date_event_end;
    }

    public function setDateEventEnd(\DateTimeImmutable $date_event_end): static
    {
        $this->date_event_end = $date_event_end;

        return $this;
    }

    public function getHourEventStart(): ?\DateTimeImmutable
    {
        return $this->hour_event_start;
    }

    public function setHourEventStart(\DateTimeImmutable $hour_event_start): static
    {
        $this->hour_event_start = $hour_event_start;

        return $this;
    }

    public function getHourEventEnd(): ?\DateTimeImmutable
    {
        return $this->hour_event_end;
    }

    public function setHourEventEnd(\DateTimeImmutable $hour_event_end): static
    {
        $this->hour_event_end = $hour_event_end;

        return $this;
    }


    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): static
    {
        $this->project = $project;

        return $this;
    }

    public function getTypeEvent(): ?TypeEvent
    {
        return $this->typeEvent;
    }

    public function setTypeEvent(?TypeEvent $typeEvent): static
    {
        $this->typeEvent = $typeEvent;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

}
