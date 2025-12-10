<?php

namespace BestWishes\Entity;

use BestWishes\Repository\GiftListPermissionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'gift_list_permissions')]
#[ORM\Index(name: 'permission_lookup_idx', columns: ['gift_list_id', 'user_id', 'permission'])]
#[ORM\UniqueConstraint(name: 'unique_permission', columns: ['gift_list_id', 'user_id', 'permission'])]
#[ORM\Entity(repositoryClass: GiftListPermissionRepository::class)]
class GiftListPermission
{
    public const PERMISSION_OWNER = 'OWNER';
    public const PERMISSION_VIEW = 'VIEW';
    public const PERMISSION_EDIT = 'EDIT';
    public const PERMISSION_DELETE = 'DELETE';
    public const PERMISSION_SURPRISE_ADD = 'SURPRISE_ADD';
    public const PERMISSION_ALERT_ADD = 'ALERT_ADD';
    public const PERMISSION_ALERT_PURCHASE = 'ALERT_PURCHASE';
    public const PERMISSION_ALERT_EDIT = 'ALERT_EDIT';
    public const PERMISSION_ALERT_DELETE = 'ALERT_DELETE';

    public const ALL_PERMISSIONS = [
        self::PERMISSION_OWNER,
        self::PERMISSION_VIEW,
        self::PERMISSION_EDIT,
        self::PERMISSION_DELETE,
        self::PERMISSION_SURPRISE_ADD,
        self::PERMISSION_ALERT_ADD,
        self::PERMISSION_ALERT_PURCHASE,
        self::PERMISSION_ALERT_EDIT,
        self::PERMISSION_ALERT_DELETE,
    ];

    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: GiftList::class)]
    #[ORM\JoinColumn(name: 'gift_list_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?GiftList $giftList = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column(name: 'permission', type: 'string', length: 50)]
    private ?string $permission = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGiftList(): ?GiftList
    {
        return $this->giftList;
    }

    public function setGiftList(GiftList $giftList): self
    {
        $this->giftList = $giftList;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getPermission(): ?string
    {
        return $this->permission;
    }

    public function setPermission(string $permission): self
    {
        $this->permission = $permission;
        return $this;
    }
}
