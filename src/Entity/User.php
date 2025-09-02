<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(['user:read'])]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups(['user:read'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Ignore]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $appartment = null;

    #[ORM\Column(length: 255,  nullable: true)]
    private ?string $phone = null;

    /**
     * @var Collection<int, Ticket>
     */
    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'author')]
    private Collection $ticketsAuthored;

    /**
     * @var Collection<int, Ticket>
     */
    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'assignee')]
    private Collection $ticketsAssigned;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'author', orphanRemoval: true)]
    private Collection $CommentAuthor;

    public function __construct()
    {
        $this->ticketsAuthored = new ArrayCollection();
        $this->ticketsAssigned = new ArrayCollection();
        $this->CommentAuthor = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
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

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0" . self::class . "\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function getAppartment(): ?string
    {
        return $this->appartment;
    }

    public function setAppartment(?string $appartment): static
    {
        $this->appartment = $appartment;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTicketsAuthored(): Collection
    {
        return $this->ticketsAuthored;
    }

    public function addTicketsAuthored(Ticket $ticketsAuthored): static
    {
        if (!$this->ticketsAuthored->contains($ticketsAuthored)) {
            $this->ticketsAuthored->add($ticketsAuthored);
            $ticketsAuthored->setAuthor($this);
        }

        return $this;
    }

    public function removeTicketsAuthored(Ticket $ticketsAuthored): static
    {
        if ($this->ticketsAuthored->removeElement($ticketsAuthored)) {
            // set the owning side to null (unless already changed)
            if ($ticketsAuthored->getAuthor() === $this) {
                $ticketsAuthored->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTicketsAssigned(): Collection
    {
        return $this->ticketsAssigned;
    }

    public function addTicketsAssigned(Ticket $ticketsAssigned): static
    {
        if (!$this->ticketsAssigned->contains($ticketsAssigned)) {
            $this->ticketsAssigned->add($ticketsAssigned);
            $ticketsAssigned->setAssignee($this);
        }

        return $this;
    }

    public function removeTicketsAssigned(Ticket $ticketsAssigned): static
    {
        if ($this->ticketsAssigned->removeElement($ticketsAssigned)) {
            // set the owning side to null (unless already changed)
            if ($ticketsAssigned->getAssignee() === $this) {
                $ticketsAssigned->setAssignee(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getCommentAuthor(): Collection
    {
        return $this->CommentAuthor;
    }

    public function addCommentAuthor(Comment $commentAuthor): static
    {
        if (!$this->CommentAuthor->contains($commentAuthor)) {
            $this->CommentAuthor->add($commentAuthor);
            $commentAuthor->setAuthor($this);
        }

        return $this;
    }

    public function removeCommentAuthor(Comment $commentAuthor): static
    {
        if ($this->CommentAuthor->removeElement($commentAuthor)) {
            // set the owning side to null (unless already changed)
            if ($commentAuthor->getAuthor() === $this) {
                $commentAuthor->setAuthor(null);
            }
        }

        return $this;
    }
}
