<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ListEvent
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ListEventRepository")
 */
class ListEvent
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
     * @var integer
     *
     * @ORM\Column(name="event_day", type="smallint", nullable=true)
     */
    private $day;

    /**
     * @var integer
     *
     * @ORM\Column(name="event_month", type="smallint", nullable=true)
     */
    private $month;

    /**
     * @var integer
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
     * @return ListEvent
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
     * Set type
     *
     * @param string $type
     *
     * @return ListEvent
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set day
     *
     * @param integer $day
     *
     * @return ListEvent
     */
    public function setDay($day)
    {
        $this->day = $day;

        return $this;
    }

    /**
     * Get day
     *
     * @return integer
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * Set month
     *
     * @param integer $month
     *
     * @return ListEvent
     */
    public function setMonth($month)
    {
        $this->month = $month;

        return $this;
    }

    /**
     * Get month
     *
     * @return integer
     */
    public function getMonth()
    {
        return $this->month;
    }


    /**
     * Set year
     *
     * @param integer $year
     *
     * @return ListEvent
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return ListEvent
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Set permanent
     *
     * @param boolean $permanent
     *
     * @return ListEvent
     */
    public function setPermanent($permanent)
    {
        $this->permanent = $permanent;

        return $this;
    }

    /**
     * Get isPermanent
     *
     * @return boolean
     */
    public function isPermanent()
    {
        return $this->permanent;
    }
}
