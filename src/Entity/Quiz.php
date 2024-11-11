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

    #[ORM\Column(length: 255, unique: true)]
    private ?string $code = null;

    #[ORM\OneToMany(targetEntity: Question::class, mappedBy: 'quiz', cascade: ['remove'])]
    private Collection $questions;

    #[ORM\OneToMany(targetEntity: LeaderBoardEntry::class, mappedBy: 'quiz', cascade: ['remove'])]
    private Collection $leaderBoardEntries;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'quizzes')]
    private User $user;

    #[ORM\Column(type: 'boolean')]
    private bool $isEnabled = true;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'quizzes')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Category $category = null;

    #[ORM\Column(type: 'boolean', nullable: true, options: ["default" => false])]
    private ?bool $withoutLeaderboard = false;

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
        $sortedQuestions = [];

        foreach ($this->questions as $question) {
            if ($question->getPosition() !== null) {
                $sortedQuestions[] = $question;
            } else {
                $sortedQuestions = $this->questions;
                return $sortedQuestions;
            }
        }

        usort($sortedQuestions, function ($a, $b) {
            return $a->getPosition() - $b->getPosition();
        });

        return $sortedQuestions;
    }

    public function addQuestions(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->setQuiz($this);
        }

        return $this;
    }

    public function getLeaderBoardEntries() {
        return $this->leaderBoardEntries;
    }

    public function addLeaderBoardEntry(Question $leaderBoardEntries): self
    {
        if (!$this->leaderBoardEntries->contains($leaderBoardEntries)) {
            $this->leaderBoardEntries[] = $leaderBoardEntries;
            $leaderBoardEntries->setQuiz($this);
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function setEnabled(bool $isEnabled): self
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function isWithoutLeaderboard(): ?bool
    {
        return $this->withoutLeaderboard;
    }

    public function setWithoutLeaderboard(?bool $withoutLeaderboard): self
    {
        $this->withoutLeaderboard = $withoutLeaderboard;

        return $this;
    }
}