<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\ORM\Mapping as ORM;

use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $text = null;

    #[ORM\Column(length: 255)]
    private ?string $answerOne = null;

    #[ORM\Column(length: 255)]
    private ?string $answerTwo = null;

    #[ORM\Column(length: 255)]
    private ?string $answerThree = null;

    #[ORM\Column(length: 255)]
    private ?string $answerFour = null;

    #[ORM\Column(length: 255)]
    private ?string $answerRight = null;

    #[ORM\ManyToOne(targetEntity: Quiz::class, inversedBy: 'questions')]
    private Quiz $quiz;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getAnswerOne(): ?string
    {
        return $this->answerOne;
    }

    public function setAnswerOne(string $answerOne): static
    {
        $this->answerOne = $answerOne;

        return $this;
    }

    public function getAnswerTwo(): ?string
    {
        return $this->answerTwo;
    }

    public function setAnswerTwo(string $answerTwo): static
    {
        $this->answerTwo = $answerTwo;

        return $this;
    }

    public function getAnswerThree(): ?string
    {
        return $this->answerThree;
    }

    public function setAnswerThree(string $answerThree): static
    {
        $this->answerThree = $answerThree;

        return $this;
    }

    public function getAnswerFour(): ?string
    {
        return $this->answerFour;
    }

    public function setAnswerFour(string $answerFour): static
    {
        $this->answerFour = $answerFour;

        return $this;
    }

    public function getAnswerRight(): ?string
    {
        return $this->answerRight;
    }

    public function setAnswerRight(string $answerRight): static
    {
        $this->answerRight = $answerRight;

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
