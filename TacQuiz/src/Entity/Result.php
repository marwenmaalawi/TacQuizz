<?php

namespace App\Entity;

use App\Repository\ResultRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ResultRepository::class)
 */
class Result
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;



    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $result;

    /**
     * @ORM\OneToMany(targetEntity=PIReply::class, mappedBy="Result")
     */
    private $pIReplies;

    /**
     * @ORM\ManyToOne(targetEntity=Quiz::class, inversedBy="results")
     */
    private $Quiz;

    public function __construct()
    {
        $this->pIReplies = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getResult(): ?float
    {
        return $this->result;
    }

    public function setResult(?float $result): self
    {
        $this->result = $result;

        return $this;
    }

    /**
     * @return Collection|PIReply[]
     */
    public function getPIReplies(): Collection
    {
        return $this->pIReplies;
    }

    public function addPIReply(PIReply $pIReply): self
    {
        if (!$this->pIReplies->contains($pIReply)) {
            $this->pIReplies[] = $pIReply;
            $pIReply->setResult($this);
        }

        return $this;
    }

    public function removePIReply(PIReply $pIReply): self
    {
        if ($this->pIReplies->removeElement($pIReply)) {
            // set the owning side to null (unless already changed)
            if ($pIReply->getResult() === $this) {
                $pIReply->setResult(null);
            }
        }

        return $this;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->Quiz;
    }

    public function setQuiz(?Quiz $Quiz): self
    {
        $this->Quiz = $Quiz;

        return $this;
    }


}
