<?php

namespace App\Entity;

use App\Repository\ReplyRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReplyRepository::class)
 */
class Reply
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
    private $cryptedId;

    /**
     * @ORM\ManyToOne(targetEntity=Choices::class, inversedBy="replies")
     */
    private $reply;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getReply(): ?Choices
    {
        return $this->reply;
    }

    public function setReply(?Choices $reply): self
    {
        $this->reply = $reply;

        return $this;
    }
}
