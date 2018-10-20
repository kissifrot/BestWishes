<?php

namespace BestWishes\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="BestWishes\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=40)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\OneToOne(targetEntity="GiftList", cascade={"remove"})
     */
    private $list;

    public function setList(?GiftList $list): void
    {
        $this->list = $list;
    }

    public function getList(): ?GiftList
    {
        return $this->list;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
