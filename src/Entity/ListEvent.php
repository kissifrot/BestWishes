<?php

namespace BestWishes\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ListEvent
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="BestWishes\Repository\ListEventRepository")
 */
class ListEvent
{
    public const BIRTHDAY_TYPE = 'birthday';

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
     * @ORM\Column(name="name", length=60)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="e_type", length=20)
     */
    private $type;

    /**
     * @var null|integer
     *
     * @ORM\Column(name="event_day", type="smallint", nullable=true)
     */
    private $day;

    /**
     * @var null|integer
     *
     * @ORM\Column(name="event_month", type="smallint", nullable=true)
     */
    private $month;

    /**
     * @var null|integer
     *
     * @ORM\Column(name="event_year", type="smallint", nullable=true)
     */
    private $year;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", options={"default":true})
     */
    private $active;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_permanent", type="boolean", options={"default":false})
     */
    private $permanent;

    public function __construct(bool $isPermanent = false, string $type = 'default')
    {
        $this->permanent = $isPermanent;
        $this->type = $type;
        $this->active = true;
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

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
        if ($type === self::BIRTHDAY_TYPE) {
            $this->permanent = true;
        }
    }

    public function isBirthday(): bool
    {
        return $this->type === self::BIRTHDAY_TYPE;
    }

    public function getDay(): ?int
    {
        return $this->day;
    }

    public function setDay(?int $day): void
    {
        $this->day = $day;
    }

    public function getMonth(): ?int
    {
        return $this->month;
    }

    public function setMonth(?int $month): void
    {
        $this->month = $month;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): void
    {
        $this->year = $year;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function isPermanent(): bool
    {
        return $this->permanent;
    }
}
