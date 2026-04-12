<?php

namespace App\Entity;

use App\Repository\ComplaintRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ComplaintRepository::class)]
class Complaint
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    #[\Symfony\Component\Validator\Constraints\NotBlank(message: "Please select an issue category.", groups: ['frontend'])]
    #[\Symfony\Component\Validator\Constraints\Choice(choices: ['Technical Issue', 'Billing & Payments', 'Account Access', 'Security & Fraud', 'Other'], message: "Invalid category selected.", groups: ['frontend'])]
    private ?string $category = null;

    #[ORM\Column(length: 255)]
    #[\Symfony\Component\Validator\Constraints\NotBlank(message: "Complaint title is required.", groups: ['frontend'])]
    #[\Symfony\Component\Validator\Constraints\Length(min: 5, max: 255, minMessage: "Your title must be at least {{ limit }} characters long.", groups: ['frontend'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[\Symfony\Component\Validator\Constraints\NotBlank(message: "Please provide a detailed description of your issue.", groups: ['frontend'])]
    #[\Symfony\Component\Validator\Constraints\Length(min: 20, minMessage: "Your description must be at least {{ limit }} characters to properly explain the issue.", groups: ['frontend'])]
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    private ?string $status = 'PENDING';

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAtFormatted(): string
    {
        return $this->createdAt->format('Y-m-d H:i');
    }

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $replyMessage = null;

    public function getReplyMessage(): ?string
    {
        return $this->replyMessage;
    }

    public function setReplyMessage(?string $replyMessage): static
    {
        $this->replyMessage = $replyMessage;

        return $this;
    }
}
