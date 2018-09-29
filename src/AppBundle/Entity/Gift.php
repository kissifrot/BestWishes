<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Gift entity
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GiftRepository")
 */
class Gift
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
     * @Assert\NotBlank()
     * @Assert\Length(min = 2)
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
     * @var null|User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="buyer_id", referencedColumnName="id", nullable=true)
     */
    private $buyer;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_received", type="boolean", options={"default":false})
     */
    private $received;

    /**
     * @var null|\DateTime
     *
     * @ORM\Column(name="received_date", type="datetime", nullable=true)
     */
    private $receivedDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_surprise", type="boolean", options={"default":false})
     */
    private $surprise;

    /**
     * @var null|\DateTime
     *
     * @ORM\Column(name="purchase_date", type="date", nullable=true)
     */
    private $purchaseDate;

    /**
     * @var null|string
     *
     * @ORM\Column(name="purchase_comment", type="text", nullable=true)
     */
    private $purchaseComment;

    /**
     * @var null|string
     *
     * @Assert\Url(checkDNS = true)
     * @ORM\Column(name="image_url", type="string", length=255, nullable=true)
     */
    private $imageUrl;

    /**
     * @var null|string
     *
     * @ORM\Column(name="image_extension", type="string", length=4, nullable=true)
     */
    private $imageExtension;

    /**
     * @var null|string
     *
     * @Assert\Url(checkDNS = true)
     * @ORM\Column(name="more_detail_url", type="string", length=255, nullable=true)
     */
    private $moreDetailUrl;

    /**
     * @var null|string
     *
     * @ORM\Column(name="more_detail", type="text", nullable=true)
     */
    private $moreDetail;

    /**
     * null|Category
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="gifts")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $category;

    public function __construct(bool $isSurprise, Category $category)
    {
        $this->addedDate = new \DateTime();
        $this->editsCount = 0;
        $this->bought = false;
        $this->received = false;
        $this->surprise = $isSurprise;
        $this->category = $category;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     * @return Gift
     */
    public function setName(?string $name): Gift
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getAddedDate(): \DateTime
    {
        return $this->addedDate;
    }

    /**
     * @param \DateTime $addedDate
     * @return Gift
     */
    public function setAddedDate(\DateTime $addedDate): Gift
    {
        $this->addedDate = $addedDate;

        return $this;
    }

    /**
     * @return int
     */
    public function getEditsCount(): int
    {
        return $this->editsCount;
    }

    /**
     * @param int $editsCount
     * @return Gift
     */
    public function setEditsCount(int $editsCount): Gift
    {
        $this->editsCount = $editsCount;

        return $this;
    }

    /**
     * @return bool
     */
    public function isBought(): bool
    {
        return $this->bought;
    }

    /**
     * @param bool $bought
     * @return Gift
     */
    public function setBought(bool $bought): Gift
    {
        $this->bought = $bought;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getBuyer(): ?User
    {
        return $this->buyer;
    }

    /**
     * @param User|null $buyer
     * @return Gift
     */
    public function setBuyer(?User $buyer): Gift
    {
        $this->buyer = $buyer;

        return $this;
    }

    /**
     * @return bool
     */
    public function isReceived(): bool
    {
        return $this->received;
    }

    /**
     * @param bool $received
     * @return Gift
     */
    public function setReceived(bool $received): Gift
    {
        $this->received = $received;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getReceivedDate(): ?\DateTime
    {
        return $this->receivedDate;
    }

    /**
     * @param \DateTime|null $receivedDate
     * @return Gift
     */
    public function setReceivedDate(?\DateTime $receivedDate): Gift
    {
        $this->receivedDate = $receivedDate;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSurprise(): bool
    {
        return $this->surprise;
    }

    /**
     * @param bool $surprise
     * @return Gift
     */
    public function setSurprise(bool $surprise): Gift
    {
        $this->surprise = $surprise;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getPurchaseDate(): ?\DateTime
    {
        return $this->purchaseDate;
    }

    /**
     * @param \DateTime|null $purchaseDate
     * @return Gift
     */
    public function setPurchaseDate(?\DateTime $purchaseDate): Gift
    {
        $this->purchaseDate = $purchaseDate;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPurchaseComment(): ?string
    {
        return $this->purchaseComment;
    }

    /**
     * @param null|string $purchaseComment
     * @return Gift
     */
    public function setPurchaseComment(?string $purchaseComment): Gift
    {
        $this->purchaseComment = $purchaseComment;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    /**
     * @param null|string $imageUrl
     * @return Gift
     */
    public function setImageUrl(?string $imageUrl): Gift
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getImageExtension(): ?string
    {
        return $this->imageExtension;
    }

    /**
     * @param null|string $imageExtension
     * @return Gift
     */
    public function setImageExtension(?string $imageExtension): Gift
    {
        $this->imageExtension = $imageExtension;

        return $this;
    }

    /**
     * @return string
     */
    public function getMoreDetailUrl(): ?string
    {
        return $this->moreDetailUrl;
    }

    /**
     * @param string $moreDetailUrl
     * @return Gift
     */
    public function setMoreDetailUrl(?string $moreDetailUrl): Gift
    {
        $this->moreDetailUrl = $moreDetailUrl;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getMoreDetail(): ?string
    {
        return $this->moreDetail;
    }

    /**
     * @param null|string $moreDetail
     * @return Gift
     */
    public function setMoreDetail(?string $moreDetail): Gift
    {
        $this->moreDetail = $moreDetail;

        return $this;
    }

    /**
     * @return null|Category
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * @param Category $category
     * @return Gift
     */
    public function setCategory(Category $category): Gift
    {
        $this->category = $category;

        return $this;
    }

    public function getCategoryId(): int
    {
        return $this->category->getId();
    }

    public function getList(): GiftList
    {
        return $this->category->getList();
    }

    public function markReceived(): void
    {
        $this->received = true;
        $this->receivedDate = new \DateTime();
    }

    public function markPurchasedBy(User $user, ?string $purchasedComment): void
    {
        $this->bought = true;
        $this->purchaseDate = new \DateTime();
        $this->buyer = $user;
        $this->purchaseComment = $purchasedComment;
    }
}
