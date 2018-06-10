<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * GiftList
 *
 * @ORM\Table(indexes={@ORM\Index(name="list_slug_idx", columns={"slug"}),@ORM\Index(name="list_name_idx", columns={"name"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GiftListRepository")
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
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(name="slug", type="string", length=50)
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


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->lastUpdate = new \DateTime();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return GiftList
     */
    public function setName(string $name): GiftList
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return GiftList
     */
    public function setSlug(string $slug): GiftList
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastUpdate(): \DateTime
    {
        return $this->lastUpdate;
    }

    /**
     * @param \DateTime $lastUpdate
     * @return GiftList
     */
    public function setLastUpdate(\DateTime $lastUpdate): GiftList
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getBirthDate(): \DateTime
    {
        return $this->birthDate;
    }

    /**
     * @param \DateTime $birthDate
     * @return GiftList
     */
    public function setBirthDate(\DateTime $birthDate): GiftList
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param mixed $owner
     * @return GiftList
     */
    public function setOwner($owner): GiftList
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories(): \Doctrine\Common\Collections\Collection
    {
        return $this->categories;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $categories
     * @return GiftList
     */
    public function setCategories(\Doctrine\Common\Collections\Collection $categories): GiftList
    {
        $this->categories = $categories;

        return $this;
    }
}
