<?php

namespace BestWishes\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Gift entity
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="BestWishes\Repository\GiftRepository")
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
     * @var null|Image
     *
     * @ORM\Embedded(class="BestWishes\Entity\Image", columnPrefix="image_")
     */
    private $image;

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
     * @ORM\Column(name="more_detail_url", length=255, nullable=true)
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
        $this->addedDate = \DateTime::createFromFormat('U', time());
        $this->editsCount = 0;
        $this->bought = false;
        $this->received = false;
        $this->surprise = $isSurprise;
        $this->category = $category;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getAddedDate(): \DateTime
    {
        return $this->addedDate;
    }

    public function getEditsCount(): int
    {
        return $this->editsCount;
    }

    public function isBought(): bool
    {
        return $this->bought;
    }

    public function getBuyer(): ?User
    {
        return $this->buyer;
    }

    public function isReceived(): bool
    {
        return $this->received;
    }

    public function getReceivedDate(): ?\DateTime
    {
        return $this->receivedDate;
    }

    public function isSurprise(): bool
    {
        return $this->surprise;
    }

    public function getPurchaseDate(): ?\DateTime
    {
        return $this->purchaseDate;
    }

    public function getPurchaseComment(): ?string
    {
        return $this->purchaseComment;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): void
    {
        $this->image = $image;
    }

    public function getMoreDetailUrl(): ?string
    {
        return $this->moreDetailUrl;
    }

    public function setMoreDetailUrl(?string $moreDetailUrl): void
    {
        $this->moreDetailUrl = $moreDetailUrl;
    }

    public function getMoreDetail(): ?string
    {
        return $this->moreDetail;
    }

    public function setMoreDetail(?string $moreDetail): void
    {
        $this->moreDetail = $moreDetail;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): void
    {
        $this->category = $category;
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
        $this->receivedDate = \DateTime::createFromFormat('U', time());
    }

    public function markPurchasedBy(User $user, ?string $purchasedComment): void
    {
        $this->bought = true;
        $this->purchaseDate = \DateTime::createFromFormat('U', time());
        $this->buyer = $user;
        $this->purchaseComment = $purchasedComment;
    }
}
