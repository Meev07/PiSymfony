<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * User Entity
 * Represents a member or administrator in the Esprit Banking system.
 * Includes security features like Password Hashing, 2FA, and Password Recovery.
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[UniqueEntity(fields: ['email'], message: 'This email is already registered.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_user')]
    private ?int $id = null;

    #[ORM\Column(name: 'first_name', length: 100)]
    #[Assert\NotBlank(message: 'Please enter your full name')]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z\s]+$/',
        message: 'The name should only contain characters and spaces.'
    )]
    private ?string $name = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Please enter your email')]
    #[Assert\Email(message: 'The email is not valid')]
    private ?string $email = null;

    #[ORM\Column(name: 'password_hash')]
    private ?string $password = null;

    #[ORM\Column(name: 'role', type: 'string')]
    private string $role = 'USER';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $dbRole = strtoupper(trim($this->role ?? 'USER'));
        
        // Explicitly map common legacy DB roles
        $roles = [];
        if ($dbRole === 'ADMIN' || $dbRole === 'ADMINISTRATEUR') {
            $roles[] = 'ROLE_ADMIN';
        }
        
        // Guarantee ROLE_USER is always present
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        // For simplicity, we take the most significant role
        if (in_array('ROLE_ADMIN', $roles)) {
            $this->role = 'ADMIN';
        } else {
            $this->role = 'USER';
        }
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    #[ORM\Column(name: 'balance', type: 'decimal', precision: 10, scale: 2, options: ['default' => 0.00])]
    private ?float $balance = 0.00;

    public function getBalance(): ?float
    {
        return (float) $this->balance;
    }

    public function setBalance(float $balance): self
    {
        $this->balance = $balance;
        return $this;
    }

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $resetToken = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $resetTokenExpiresAt = null;

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;
        return $this;
    }

    public function getResetTokenExpiresAt(): ?\DateTimeInterface
    {
        return $this->resetTokenExpiresAt;
    }

    public function setResetTokenExpiresAt(?\DateTimeInterface $resetTokenExpiresAt): self
    {
        $this->resetTokenExpiresAt = $resetTokenExpiresAt;
        return $this;
    }

    #[ORM\Column(name: 'profile_image', length: 500, nullable: true)]
    private ?string $profileImage = null;

    public function getProfileImage(): ?string
    {
        return $this->profileImage;
    }

    public function setProfileImage(?string $profileImage): self
    {
        $this->profileImage = $profileImage;
        return $this;
    }

    #[ORM\Column(length: 6, nullable: true)]
    private ?string $otpCode = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $otpExpiresAt = null;

    public function getOtpCode(): ?string
    {
        return $this->otpCode;
    }

    public function setOtpCode(?string $otpCode): self
    {
        $this->otpCode = $otpCode;
        return $this;
    }

    public function getOtpExpiresAt(): ?\DateTimeInterface
    {
        return $this->otpExpiresAt;
    }

    public function setOtpExpiresAt(?\DateTimeInterface $otpExpiresAt): self
    {
        $this->otpExpiresAt = $otpExpiresAt;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }
}
