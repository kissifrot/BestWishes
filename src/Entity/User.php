<?php

namespace BestWishes\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 */
#[ORM\Table]
#[ORM\Entity(repositoryClass: \BestWishes\Repository\UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface, \Stringable
{
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(type: 'string', length: 100, unique: true)]
    private ?string $username = null;

    /** @var string[] */
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $lastLogin = null;

    #[ORM\Column(type: 'string')]
    private ?string $password = null;

    #[ORM\Column(type: 'string', length: 180)]
    private ?string $email = null;

    #[ORM\Column(name: 'name', type: 'string', length: 40)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\OneToOne(targetEntity: GiftList::class, cascade: ['remove'])]
    private ?GiftList $list = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->getUserIdentifier();
    }

    public function setList(?GiftList $list): void
    {
        $this->list = $list;
    }

    public function getList(): ?GiftList
    {
        return $this->list;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @deprecated use getUserIdentifier() instead
     */
    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    /**
     * @deprecated use setUserIdentifier() instead
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    public function setUserIdentifier(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function markLoggedIn(): void
    {
        $this->lastLogin = \DateTimeImmutable::createFromFormat('U', (string) time());
    }

    public function setLastLogin(?\DateTimeImmutable $time): void
    {
        $this->lastLogin = $time;
    }

    public function getLastLogin(): ?\DateTimeImmutable
    {
        return $this->lastLogin;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function eraseCredentials(): void
    {
    }
}
