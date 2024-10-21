<?php

namespace BestWishes\Entity;

use BestWishes\Repository\GiftRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Clock\DatePoint;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table]
#[ORM\Entity(repositoryClass: GiftRepository::class)]
class Gift
{
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2)]
    #[ORM\Column(name: 'name', type: 'string', length: 150)]
    private ?string $name = null;

    #[ORM\Column(name: 'added_date', type: 'date_immutable')]
    private readonly \DateTimeImmutable $addedDate;

    #[ORM\Column(name: 'edits_count', type: 'smallint', options: ['default' => 0])]
    private int $editsCount = 0;

    #[ORM\Column(name: 'is_bought', type: 'boolean', options: ['default' => false])]
    private bool $bought = false;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'buyer_id', referencedColumnName: 'id', nullable: true)]
    private ?User $buyer = null;

    #[ORM\Column(name: 'is_received', type: 'boolean', options: ['default' => false])]
    private bool $received = false;

    #[ORM\Column(name: 'received_date', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $receivedDate = null;

    #[ORM\Column(name: 'is_surprise', type: 'boolean', options: ['default' => false])]
    private readonly bool $surprise;

    #[ORM\Embedded(class: Image::class, columnPrefix: 'image_')]
    private ?Image $image = null;

    #[ORM\Column(name: 'purchase_date', type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $purchaseDate = null;

    #[ORM\Column(name: 'purchase_comment', type: 'text', nullable: true)]
    private ?string $purchaseComment = null;

    #[Assert\Url]
    #[ORM\Column(name: 'more_detail_url', length: 255, nullable: true)]
    private ?string $moreDetailUrl = null;

    #[ORM\Column(name: 'more_detail', type: 'text', nullable: true)]
    private ?string $moreDetail = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'gifts')]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Category $category;

    public function __construct(bool $isSurprise, Category $category)
    {
        $this->addedDate = new DatePoint();
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

    public function getAddedDate(): \DateTimeImmutable
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

    public function getReceivedDate(): ?\DateTimeImmutable
    {
        return $this->receivedDate;
    }

    public function isSurprise(): bool
    {
        return $this->surprise;
    }

    public function getPurchaseDate(): ?\DateTimeImmutable
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
        $this->receivedDate = new DatePoint();
    }

    public function markPurchasedBy(User $user, ?string $purchasedComment): void
    {
        $this->bought = true;
        $this->purchaseDate = new DatePoint();
        $this->buyer = $user;
        $this->purchaseComment = $purchasedComment;
    }
}
