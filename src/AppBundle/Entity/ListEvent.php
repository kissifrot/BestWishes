<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ListEvent
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ListEventRepository")
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
     * @ORM\Column(name="name", type="string", length=60)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="e_type", type="string", length=20)
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

    public function __construct(bool $isPermanent = false)
    {
        $this->active = true;
        $this->permanent = $isPermanent;
        $this->type = 'default';
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
     * @return ListEvent
     */
    public function setName(?string $name): ListEvent
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return ListEvent
     */
    public function setType(string $type): ListEvent
    {
        $this->type = $type;
        if ($type === self::BIRTHDAY_TYPE) {
            $this->permanent = true;
        }

        return $this;
    }

    /**
     * @return int|null
     */
    public function getDay(): ?int
    {
        return $this->day;
    }

    /**
     * @param int|null $day
     * @return ListEvent
     */
    public function setDay(?int $day): ListEvent
    {
        $this->day = $day;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getMonth(): ?int
    {
        return $this->month;
    }

    /**
     * @param int|null $month
     * @return ListEvent
     */
    public function setMonth(?int $month): ListEvent
    {
        $this->month = $month;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getYear(): ?int
    {
        return $this->year;
    }

    /**
     * @param int|null $year
     * @return ListEvent
     */
    public function setYear(?int $year): ListEvent
    {
        $this->year = $year;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     * @return ListEvent
     */
    public function setActive(bool $active): ListEvent
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPermanent(): bool
    {
        return $this->permanent;
    }

    /**
     * @param bool $permanent
     * @return ListEvent
     */
    public function setPermanent(bool $permanent): ListEvent
    {
        $this->permanent = $permanent;

        return $this;
    }
}
