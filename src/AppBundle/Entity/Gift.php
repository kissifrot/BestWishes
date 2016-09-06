<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Gift entity
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\GiftRepository")
 */
class Gift
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
     * @ORM\Column(name="name", type="string", length=150)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="added_date", type="date")
     */
    private $addedDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="edits_count", type="smallint", options={"default":0})
     */
    private $editsCount;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_bought", type="boolean", options={"default":false})
     */
    private $bought;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_received", type="boolean", options={"default":false})
     */
    private $received;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_surprise", type="boolean", options={"default":false})
     */
    private $surprise;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="purchase_date", type="date", nullable=true)
     */
    private $purchaseDate;

    /**
     * @var string
     *
     * @ORM\Column(name="purchase_comment", type="text", nullable=true)
     */
    private $purchaseComment;

    /**
     * @var string
     *
     * @ORM\Column(name="image_filename", type="string", length=150, nullable=true)
     */
    private $imageFilename;

    /**
     * @var string
     *
     * @ORM\Column(name="more_detail", type="string", length=255, nullable=true)
     */
    private $moreDetail;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="gifts")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $category;

    public function __construct()
    {
        $this->addedDate = new \DateTime();
        $this->editsCount = 0;
        $this->bought = false;
        $this->received = false;
        $this->surprise = false;
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
     * @return Gift
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
     * Set addedDate
     *
     * @param \DateTime $addedDate
     *
     * @return Gift
     */
    public function setAddedDate($addedDate)
    {
        $this->addedDate = $addedDate;

        return $this;
    }

    /**
     * Get addedDate
     *
     * @return \DateTime
     */
    public function getAddedDate()
    {
        return $this->addedDate;
    }

    /**
     * Set editsCount
     *
     * @param integer $editsCount
     *
     * @return Gift
     */
    public function setEditsCount($editsCount)
    {
        $this->editsCount = $editsCount;

        return $this;
    }

    /**
     * Get editsCount
     *
     * @return integer
     */
    public function getEditsCount()
    {
        return $this->editsCount;
    }

    /**
     * Set bought
     *
     * @param boolean $bought
     *
     * @return Gift
     */
    public function setBought($bought)
    {
        $this->bought = $bought;

        return $this;
    }

    /**
     * Get bought
     *
     * @return boolean
     */
    public function isBought()
    {
        return $this->bought;
    }

    /**
     * Set received
     *
     * @param boolean $received
     *
     * @return Gift
     */
    public function setReceived($received)
    {
        $this->received = $received;

        return $this;
    }

    /**
     * Get received
     *
     * @return boolean
     */
    public function isReceived()
    {
        return $this->received;
    }

    /**
     * Set surprise
     *
     * @param boolean $surprise
     *
     * @return Gift
     */
    public function setSurprise($surprise)
    {
        $this->surprise = $surprise;

        return $this;
    }

    /**
     * Get surprise
     *
     * @return boolean
     */
    public function isSurprise()
    {
        return $this->surprise;
    }

    /**
     * Set purchaseDate
     *
     * @param \DateTime $purchaseDate
     *
     * @return Gift
     */
    public function setPurchaseDate($purchaseDate)
    {
        $this->purchaseDate = $purchaseDate;

        return $this;
    }

    /**
     * Get purchaseDate
     *
     * @return \DateTime
     */
    public function getPurchaseDate()
    {
        return $this->purchaseDate;
    }

    /**
     * Set purchaseComment
     *
     * @param string $purchaseComment
     *
     * @return Gift
     */
    public function setPurchaseComment($purchaseComment)
    {
        $this->purchaseComment = $purchaseComment;

        return $this;
    }

    /**
     * Get purchaseComment
     *
     * @return string
     */
    public function getPurchaseComment()
    {
        return $this->purchaseComment;
    }

    /**
     * Set imageFilename
     *
     * @param string $imageFilename
     *
     * @return Gift
     */
    public function setImageFilename($imageFilename)
    {
        $this->imageFilename = $imageFilename;

        return $this;
    }

    /**
     * Get imageFilename
     *
     * @return string
     */
    public function getImageFilename()
    {
        return $this->imageFilename;
    }

    /**
     * Set moreDetail
     *
     * @param string $moreDetail
     *
     * @return Gift
     */
    public function setMoreDetail($moreDetail)
    {
        $this->moreDetail = $moreDetail;

        return $this;
    }

    /**
     * Get moreDetail
     *
     * @return string
     */
    public function getMoreDetail()
    {
        return $this->moreDetail;
    }

    /**
     * Set category
     *
     * @param Category $category
     *
     * @return Gift
     */
    public function setCategory(Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }
}
