<?php

namespace App\Entity;

use App\Repository\PersonalInformationsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PersonalInformationsRepository::class)
 */
class PersonalInformations
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
    private $information;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=Quiz::class, inversedBy="personalInformations")
     */
    private $quiz;

    /**
     * @ORM\OneToMany(targetEntity=PIReply::class, mappedBy="PI")
     */
    private $pIReplies;



    public function __construct()
    {
        $this->pIReplies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInformation(): ?string
    {
        return $this->information;
    }

    public function setInformation(?string $information): self
    {
        $this->information = $information;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

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
            $pIReply->setPI($this);
        }

        return $this;
    }

    public function removePIReply(PIReply $pIReply): self
    {
        if ($this->pIReplies->removeElement($pIReply)) {
            // set the owning side to null (unless already changed)
            if ($pIReply->getPI() === $this) {
                $pIReply->setPI(null);
            }
        }

        return $this;
    }


}
