<?php

namespace BestWishes\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Category
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="BestWishes\Repository\CategoryRepository")
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
     * @ORM\Column(name="name", length=150)
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

    public function __construct(int $givenId = null)
    {
        $this->gifts = new ArrayCollection();
        $this->visible = true;
        $this->id = $givenId;
    }

    public function __toString()
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

    public function setName($name): void
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

    public function getGifts(): \Doctrine\Common\Collections\Collection
    {
        return $this->gifts;
    }

    public function getTotalGiftsCount(): int
    {
        return $this->gifts->count();
    }

    public function getViewableGiftsCount(): int
    {
        return $this->gifts->filter(function (Gift $gift) {
            return !$gift->isSurprise();
        })->count();
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
