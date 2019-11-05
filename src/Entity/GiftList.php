<?php

namespace BestWishes\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * GiftList
 *
 * @ORM\Table(indexes={@ORM\Index(name="list_slug_idx", columns={"slug"}),@ORM\Index(name="list_name_idx", columns={"name"})})
 * @ORM\Entity(repositoryClass="BestWishes\Repository\GiftListRepository")
 */
class GiftList
{
    /**
     * @var null|integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", length=50)
     */
    private $name;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(name="slug", length=50)
     */
    private $slug;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(name="last_update", type="date_immutable")
     */
    private $lastUpdate;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(name="birthdate", type="date_immutable")
     */
    private $birthDate;

    /**
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $owner;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Category", mappedBy="list")
     */
    private $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->lastUpdate = new \DateTimeImmutable();
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

    public function getOwner()
    {
        return $this->owner;
    }

    public function setOwner($owner): void
    {
        $this->owner = $owner;
    }

    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function setCategories(Collection $categories): void
    {
        $this->categories = $categories;
    }
}
