<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Category
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 */
class Category
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
     * @var null|string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min = 2)
     * @ORM\Column(name="name", type="string", length=150)
     */
    private $name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_visible", type="boolean")
     */
    private $visible;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Gift", mappedBy="category")
     */
    private $gifts;

    /**
     * @var GiftList
     * @ORM\ManyToOne(targetEntity="GiftList", inversedBy="categories")
     * @ORM\JoinColumn(name="list_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $list;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->gifts = new ArrayCollection();
        $this->visible = true;
    }

    public function __toString()
    {
        return "Name: {$this->name}, Id: {$this->id}";
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Category
     */
    public function setName($name): Category
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return Category
     */
    public function setVisible(bool $visible): Category
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean
     */
    public function isVisible(): bool
    {
        return $this->visible;
    }

    /**
     * Add gifts
     *
     * @param Gift $gifts
     *
     * @return Category
     */
    public function addGift(Gift $gifts): Category
    {
        if (!$this->gifts->contains($gifts)) {
            $this->gifts->add($gifts);
        }

        return $this;
    }

    /**
     * Remove gifts
     *
     * @param Gift $gifts
     */
    public function removeGift(Gift $gifts): void
    {
        $this->gifts->removeElement($gifts);
    }

    /**
     * Get gifts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGifts(): \Doctrine\Common\Collections\Collection
    {
        return $this->gifts;
    }

    /**
     * Set list
     *
     * @param GiftList $list
     *
     * @return Category
     */
    public function setList(?GiftList $list): Category
    {
        $this->list = $list;

        return $this;
    }

    /**
     * Get list
     *
     * @return GiftList
     */
    public function getList(): GiftList
    {
        return $this->list;
    }
}
