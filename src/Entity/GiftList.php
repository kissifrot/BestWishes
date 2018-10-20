<?php

namespace BestWishes\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
     * @var \DateTime
     *
     * @ORM\Column(name="last_update", type="date")
     */
    private $lastUpdate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthdate", type="date")
     */
    private $birthDate;

    /**
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $owner;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Category", mappedBy="list")
     */
    private $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->lastUpdate = new \DateTime();
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

    public function getLastUpdate(): \DateTime
    {
        return $this->lastUpdate;
    }

    public function setLastUpdate(\DateTime $lastUpdate): void
    {
        $this->lastUpdate = $lastUpdate;
    }

    public function getBirthDate(): \DateTime
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTime $birthDate): void
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

    public function getCategories(): \Doctrine\Common\Collections\Collection
    {
        return $this->categories;
    }

    public function setCategories(\Doctrine\Common\Collections\Collection $categories): void
    {
        $this->categories = $categories;
    }
}
