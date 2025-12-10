<?php

namespace BestWishes\Entity;

use BestWishes\Repository\GiftListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Clock\DatePoint;

#[ORM\Table]
#[ORM\Index(name: 'list_slug_idx', columns: ['slug'])]
#[ORM\Index(name: 'list_name_idx', columns: ['name'])]
#[ORM\Entity(repositoryClass: GiftListRepository::class)]
class GiftList
{
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(name: 'name', length: 50)]
    private string $name;

    #[Gedmo\Slug(fields: ['name'])]
    #[ORM\Column(name: 'slug', length: 50)]
    private string $slug;

    #[ORM\Column(name: 'last_update', type: 'date_immutable')]
    private \DateTimeImmutable $lastUpdate;

    #[ORM\Column(name: 'birthdate', type: 'date_immutable')]
    private \DateTimeImmutable $birthDate;

    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'owner_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $owner;

    /** @var Collection<int, Category> */
    #[ORM\OneToMany(targetEntity: Category::class, mappedBy: 'list')]
    private Collection $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->lastUpdate = new DatePoint();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getLastUpdate(): \DateTimeImmutable
    {
        return $this->lastUpdate;
    }

    public function setLastUpdate(\DateTimeImmutable $lastUpdate): void
    {
        $this->lastUpdate = $lastUpdate;
    }

    public function getBirthDate(): \DateTimeImmutable
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeImmutable $birthDate): void
    {
        $this->birthDate = $birthDate;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): void
    {
        $this->owner = $owner;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * @param Collection<int, Category> $categories
     */
    public function setCategories(Collection $categories): void
    {
        $this->categories = $categories;
    }
}
