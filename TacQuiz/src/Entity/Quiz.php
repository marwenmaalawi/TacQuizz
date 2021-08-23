<?php

namespace App\Entity;

use App\Repository\QuizRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=QuizRepository::class)
 */
class Quiz
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Title;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date_quiz;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;



    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="quizzes")
     */
    private $Category;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="quizzes")
     */
    private $user;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $random;

    /**
     * @ORM\OneToMany(targetEntity=Question::class, mappedBy="quiz")
     */
    private $questions;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $public;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $publicResult;

    /**
     * @ORM\OneToMany(targetEntity=PersonalInformations::class, mappedBy="quiz")
     */
    private $personalInformations;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $timer;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $Quiztime;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $minScore;

    /**
     * @ORM\OneToMany(targetEntity=Result::class, mappedBy="Quiz")
     */
    private $results;


    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->personalInformations = new ArrayCollection();
        $this->results = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->Title;
    }

    public function setTitle(?string $Title): self
    {
        $this->Title = $Title;

        return $this;
    }

    public function getDateQuiz(): ?\DateTimeInterface
    {
        return $this->date_quiz;
    }

    public function setDateQuiz(?\DateTimeInterface $date_quiz): self
    {
        $this->date_quiz = $date_quiz;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }





    public function getCategory(): ?Category
    {
        return $this->Category;
    }

    public function setCategory(?Category $Category): self
    {
        $this->Category = $Category;

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

    public function getRandom(): ?bool
    {
        return $this->random;
    }

    public function setRandom(?bool $random): self
    {
        $this->random = $random;

        return $this;
    }

    /**
     * @return Collection|Question[]
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->setQuiz($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getQuiz() === $this) {
                $question->setQuiz(null);
            }
        }

        return $this;
    }

    public function getPublic(): ?bool
    {
        return $this->public;
    }

    public function setPublic(?bool $public): self
    {
        $this->public = $public;

        return $this;
    }

    public function getPublicResult(): ?bool
    {
        return $this->publicResult;
    }

    public function setPublicResult(?bool $publicResult): self
    {
        $this->publicResult = $publicResult;

        return $this;
    }

    /**
     * @return Collection|PersonalInformations[]
     */
    public function getPersonalInformations(): Collection
    {
        return $this->personalInformations;
    }

    public function addPersonalInformation(PersonalInformations $personalInformation): self
    {
        if (!$this->personalInformations->contains($personalInformation)) {
            $this->personalInformations[] = $personalInformation;
            $personalInformation->setQuiz($this);
        }

        return $this;
    }

    public function removePersonalInformation(PersonalInformations $personalInformation): self
    {
        if ($this->personalInformations->removeElement($personalInformation)) {
            // set the owning side to null (unless already changed)
            if ($personalInformation->getQuiz() === $this) {
                $personalInformation->setQuiz(null);
            }
        }

        return $this;
    }

    public function getTimer(): ?bool
    {
        return $this->timer;
    }

    public function setTimer(?bool $timer): self
    {
        $this->timer = $timer;

        return $this;
    }

    public function getQuiztime(): ?int
    {
        return $this->Quiztime;
    }

    public function setQuiztime(?int $Quiztime): self
    {
        $this->Quiztime = $Quiztime;

        return $this;
    }

    public function getMinScore(): ?int
    {
        return $this->minScore;
    }

    public function setMinScore(?int $minScore): self
    {
        $this->minScore = $minScore;

        return $this;
    }

    /**
     * @return Collection|Result[]
     */
    public function getResults(): Collection
    {
        return $this->results;
    }

    public function addResult(Result $result): self
    {
        if (!$this->results->contains($result)) {
            $this->results[] = $result;
            $result->setQuiz($this);
        }

        return $this;
    }

    public function removeResult(Result $result): self
    {
        if ($this->results->removeElement($result)) {
            // set the owning side to null (unless already changed)
            if ($result->getQuiz() === $this) {
                $result->setQuiz(null);
            }
        }

        return $this;
    }



}
