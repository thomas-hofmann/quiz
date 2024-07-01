<?php

namespace App\Entity;

use App\Repository\LeaderBoardEntryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LeaderBoardEntryRepository::class)]
class LeaderBoardEntry
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $matrikelnumber = null;

    #[ORM\Column(type: 'integer')]
    private ?int $score = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $allAnswers = null;

    #[ORM\ManyToOne(targetEntity: Quiz::class, inversedBy: 'leaderBoardEntries')]
    private Quiz $quiz;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMatrikelnumber(): ?string
    {
        return $this->matrikelnumber;
    }

    public function setMatrikelnumber(string $matrikelnumber): static
    {
        $this->matrikelnumber = $matrikelnumber;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function getAllAnswers(): ?array
    {
        return $this->allAnswers;
    }

    public function setAllAnswers(?array $allAnswers): static
    {
        $this->allAnswers = $allAnswers;

        return $this;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): self
    {
        $this->quiz = $quiz;

        return $this;
    }
}
