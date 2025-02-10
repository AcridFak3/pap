<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    private ?User $userId = null;

    #[ORM\Column(length: 30)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 1)]
    private ?string $status = 'p';

    #[ORM\Column(length: 1)]
    private ?string $priority = 'l';

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'ticketId', cascade: ['remove'])]
    private Collection $comments;

    /**
     * @var Collection<int, TicketLog>
     */
    #[ORM\OneToMany(targetEntity: TicketLog::class, mappedBy: 'ticket', cascade: ['remove'])]
    private Collection $ticketLogs;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->ticketLogs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?User
    {
        return $this->userId;
    }

    public function setUserId(?User $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getPriority(): ?string
    {
        return $this->priority;
    }

    public function setPriority(string $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setTicketId($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getTicketId() === $this) {
                $comment->setTicketId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TicketLog>
     */
    public function getTicketLogs(): Collection
    {
        return $this->ticketLogs;
    }

    public function addTicketLog(TicketLog $ticketLog): static
    {
        if (!$this->ticketLogs->contains($ticketLog)) {
            $this->ticketLogs->add($ticketLog);
            $ticketLog->setTicket($this);
        }

        return $this;
    }

    public function removeTicketLog(TicketLog $ticketLog): static
    {
        if ($this->ticketLogs->removeElement($ticketLog)) {
            // set the owning side to null (unless already changed)
            if ($ticketLog->getTicket() === $this) {
                $ticketLog->setTicket(null);
            }
        }

        return $this;
    }
}
