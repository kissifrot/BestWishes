<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Category
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\CategoryRepository")
 */
class Category
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Gift", mappedBy="category")
     */
    protected $gifts;

    /**
     * @ORM\ManyToOne(targetEntity="GiftList", inversedBy="categories")
     * @ORM\JoinColumn(name="list_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $list;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->gifts = new ArrayCollection();
        $this->visible = true;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
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
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
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
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean
     */
    public function isVisible()
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
    public function addGift(Gift $gifts)
    {
        if (!$this->gifts->contains($gifts)) {
            $this->gifts[] = $gifts;
        }

        return $this;
    }

    /**
     * Remove gifts
     *
     * @param Gift $gifts
     */
    public function removeGift(Gift $gifts)
    {
        $this->gifts->removeElement($gifts);
    }

    /**
     * Get gifts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGifts()
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
    public function setList(GiftList $list = null)
    {
        $this->list = $list;

        return $this;
    }

    /**
     * Get list
     *
     * @return GiftList
     */
    public function getList()
    {
        return $this->list;
    }
}
