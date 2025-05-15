<?php
// src/Entity/MessageThread.php
namespace App\Entity;

use App\Repository\MessageThreadRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageThreadRepository::class)]
class MessageThread
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ClientCase $case = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $sender = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $recipient = null;

    #[ORM\Column(type: 'text')]
    private ?string $message = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isRead = false;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $sentAt = null;

    public function getId(): ?int { return $this->id; }

    public function getCase(): ?ClientCase { return $this->case; }
    public function setCase(?ClientCase $case): static { $this->case = $case; return $this; }

    public function getSender(): ?User { return $this->sender; }
    public function setSender(?User $sender): static { $this->sender = $sender; return $this; }

    public function getRecipient(): ?User { return $this->recipient; }
    public function setRecipient(?User $recipient): static { $this->recipient = $recipient; return $this; }

    public function getMessage(): ?string { return $this->message; }
    public function setMessage(string $message): static { $this->message = $message; return $this; }

    public function getIsRead(): bool { return $this->isRead; }
    public function setIsRead(bool $isRead): static { $this->isRead = $isRead; return $this; }

    public function getSentAt(): ?\DateTimeInterface { return $this->sentAt; }
    public function setSentAt(\DateTimeInterface $sentAt): static { $this->sentAt = $sentAt; return $this; }
}
