<?php

namespace App\Entity;

use App\Repository\QuizRepository;
use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: QuizRepository::class)]
class Quiz
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\OneToMany(targetEntity: Question::class, mappedBy: 'quiz')]
    private Collection $questions;

    #[ORM\OneToMany(targetEntity: LeaderBoardEntry::class, mappedBy: 'quiz')]
    private Collection $leaderBoardEntries;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->leaderBoardEntries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getQuestions() {
        return $this->questions;
    }

    public function addQuestions(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->setQuiz($this);
        }

        return $this;
    }

    public function addLeaderBoardEntry(Question $leaderBoardEntries): self
    {
        if (!$this->leaderBoardEntries->contains($leaderBoardEntries)) {
            $this->leaderBoardEntries[] = $leaderBoardEntries;
            $leaderBoardEntries->setQuiz($this);
        }

        return $this;
    }

}
