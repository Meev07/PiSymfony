<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(message: "Email is required")]
    #[Assert\Email(message: "The email '{{ value }}' is not a valid email")]
    private ?string $email = null;


    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "First name is required")]
    #[Assert\Length(min: 2, max: 50, minMessage: "First name must be at least {{ limit }} characters long", maxMessage: "First name cannot be longer than {{ limit }} characters")]
    #[Assert\Regex(pattern: "/^[a-zA-Z\s\-]+$/", message: "First name can only contain letters, spaces, or hyphens")]
    private ?string $firstName = null;


    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Last name is required")]
    #[Assert\Length(min: 2, max: 50, minMessage: "Last name must be at least {{ limit }} characters long", maxMessage: "Last name cannot be longer than {{ limit }} characters")]
    #[Assert\Regex(pattern: "/^[a-zA-Z\s\-]+$/", message: "Last name can only contain letters, spaces, or hyphens")]
    private ?string $lastName = null;


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profileImage = null;

    #[ORM\Column]
    private ?bool $isVerified = false;

    #[ORM\Column]
    private bool $isActive = true;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 6, nullable: true)]
    private ?string $loginCode = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $loginCodeExpiresAt = null;

    #[ORM\Column]
    private bool $is2faEnabled = false;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $faceIdDescriptor = null;

    #[ORM\Column]
    private bool $isFaceIdEnabled = false;

    #[ORM\Column(length: 6, nullable: true)]
    private ?string $verificationCode = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $verificationCodeExpiresAt = null;



    /**
     * @var Collection<int, Account>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Account::class, orphanRemoval: true)]
    private Collection $accounts;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->accounts = new ArrayCollection();
    }

    /**
     * @return Collection<int, Account>
     */
    public function getAccounts(): Collection
    {
        return $this->accounts;
    }

    public function addAccount(Account $account): static
    {
        if (!$this->accounts->contains($account)) {
            $this->accounts->add($account);
            $account->setUser($this);
        }

        return $this;
    }

    public function removeAccount(Account $account): static
    {
        if ($this->accounts->removeElement($account)) {
            // set the owning side to null (unless already changed)
            if ($account->getUser() === $this) {
                $account->setUser(null);
            }
        }

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function getProfileImage(): ?string
    {
        return $this->profileImage;
    }

    public function setProfileImage(?string $profileImage): static
    {
        $this->profileImage = $profileImage;

        return $this;
    }

    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

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
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function getLoginCode(): ?string
    {
        return $this->loginCode;
    }

    public function setLoginCode(?string $loginCode): static
    {
        $this->loginCode = $loginCode;
        return $this;
    }

    public function getLoginCodeExpiresAt(): ?\DateTimeImmutable
    {
        return $this->loginCodeExpiresAt;
    }

    public function setLoginCodeExpiresAt(?\DateTimeImmutable $loginCodeExpiresAt): static
    {
        $this->loginCodeExpiresAt = $loginCodeExpiresAt;
        return $this;
    }

    public function isIs2faEnabled(): bool
    {
        return $this->is2faEnabled;
    }

    public function setIs2faEnabled(bool $is2faEnabled): static
    {
        $this->is2faEnabled = $is2faEnabled;
        return $this;
    }

    public function getFaceIdDescriptor(): ?array
    {
        return $this->faceIdDescriptor;
    }

    public function setFaceIdDescriptor(?array $faceIdDescriptor): static
    {
        $this->faceIdDescriptor = $faceIdDescriptor;
        return $this;
    }

    public function isFaceIdEnabled(): bool
    {
        return $this->isFaceIdEnabled;
    }

    public function setIsFaceIdEnabled(bool $isFaceIdEnabled): static
    {
        $this->isFaceIdEnabled = $isFaceIdEnabled;
        return $this;
    }

    public function getVerificationCode(): ?string
    {
        return $this->verificationCode;
    }

    public function setVerificationCode(?string $verificationCode): static
    {
        $this->verificationCode = $verificationCode;
        return $this;
    }

    public function getVerificationCodeExpiresAt(): ?\DateTimeImmutable
    {
        return $this->verificationCodeExpiresAt;
    }

    public function setVerificationCodeExpiresAt(?\DateTimeImmutable $verificationCodeExpiresAt): static
    {
        $this->verificationCodeExpiresAt = $verificationCodeExpiresAt;
        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->firstName . ' ' . $this->lastName . ' (' . $this->email . ')';
    }
}
