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
    public const string PERMISSION_OWNER = 'OWNER';
    public const string PERMISSION_VIEW = 'VIEW';
    public const string PERMISSION_EDIT = 'EDIT';
    public const string PERMISSION_DELETE = 'DELETE';
    public const string PERMISSION_SURPRISE_ADD = 'SURPRISE_ADD';
    public const string PERMISSION_ALERT_ADD = 'ALERT_ADD';
    public const string PERMISSION_ALERT_PURCHASE = 'ALERT_PURCHASE';
    public const string PERMISSION_ALERT_EDIT = 'ALERT_EDIT';
    public const string PERMISSION_ALERT_DELETE = 'ALERT_DELETE';

    public const array ALERT_PERMISSIONS = [
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
    private GiftList $giftList;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(name: 'permission', type: 'string', length: 50)]
    private string $permission;

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
