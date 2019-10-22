<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rate;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\user", inversedBy="comments")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Announce", inversedBy="comments")
     * @JoinColumn(name="announce_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $announce;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRate(): ?int
    {
        return $this->rate;
    }

    public function setRate(?int $rate): self
    {
        $this->rate = $rate;

        return $this;
    }


    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getAnnounce(): ?announce
    {
        return $this->announce;
    }

    public function setAnnounce(?announce $announce): self
    {
        $this->announce = $announce;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function __toString()
    {
        return $this->content;
    }
}
