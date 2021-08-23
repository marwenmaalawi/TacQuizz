<?php

namespace App\Entity;

use App\Repository\PIReplyRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PIReplyRepository::class)
 */
class PIReply
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
    private $reply;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cryptedId;

    /**
     * @ORM\ManyToOne(targetEntity=PersonalInformations::class, inversedBy="pIReplies")
     */
    private $PI;

    /**
     * @ORM\ManyToOne(targetEntity=Result::class, inversedBy="pIReplies")
     */
    private $Result;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReply(): ?string
    {
        return $this->reply;
    }

    public function setReply(?string $reply): self
    {
        $this->reply = $reply;

        return $this;
    }

    public function getCryptedId(): ?string
    {
        return $this->cryptedId;
    }

    public function setCryptedId(?string $cryptedId): self
    {
        $this->cryptedId = $cryptedId;

        return $this;
    }

    public function getPI(): ?PersonalInformations
    {
        return $this->PI;
    }

    public function setPI(?PersonalInformations $PI): self
    {
        $this->PI = $PI;

        return $this;
    }

    public function getResult(): ?Result
    {
        return $this->Result;
    }

    public function setResult(?Result $Result): self
    {
        $this->Result = $Result;

        return $this;
    }


}
