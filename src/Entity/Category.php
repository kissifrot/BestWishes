<?php

namespace BestWishes\Entity;

use BestWishes\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table]
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category implements \Stringable
{
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2)]
    #[ORM\Column(name: 'name', length: 150)]
    private ?string $name = null;

    #[ORM\Column(name: 'is_visible', type: 'boolean')]
    private bool $visible = true;

    /** @var Collection<int, Gift> */
    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Gift::class)]
    private Collection $gifts;

    #[ORM\ManyToOne(targetEntity: GiftList::class, inversedBy: 'categories')]
    #[ORM\JoinColumn(name: 'list_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?GiftList $list = null;

    public function __construct()
    {
        $this->gifts = new ArrayCollection();
    }

    public function __toString(): string
    {
        return sprintf(
            'Name: %s, Id: %u',
            $this->name,
            $this->id
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setVisible(bool $visible): void
    {
        $this->visible = $visible;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function addGift(Gift $gifts): void
    {
        if (!$this->gifts->contains($gifts)) {
            $this->gifts->add($gifts);
        }
    }

    public function removeGift(Gift $gifts): void
    {
        $this->gifts->removeElement($gifts);
    }

    /**
     * @return Collection<int, Gift>
     */
    public function getGifts(): Collection
    {
        return $this->gifts;
    }

    public function getTotalGiftsCount(): int
    {
        return $this->gifts->count();
    }

    public function getViewableGiftsCount(): int
    {
        return $this->gifts->filter(fn (Gift $gift) => !$gift->isSurprise())->count();
    }

    public function setList(?GiftList $list): void
    {
        $this->list = $list;
    }

    public function getList(): GiftList
    {
        return $this->list;
    }
}
