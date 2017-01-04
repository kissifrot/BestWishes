<?php

namespace AppBundle\Event;

use AppBundle\Entity\Gift;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\User\UserInterface;

class GiftCreatedEvent extends Event
{
    const NAME = 'gift.created';

    /**
     * @var Gift
     */
    protected $gift;

    /**
     * @var UserInterface
     */
    private $creator;

    public function __construct(Gift $gift, UserInterface $creator)
    {
        $this->gift = $gift;
        $this->creator = $creator;
    }

    /**
     * @return Gift
     */
    public function getGift()
    {
        return $this->gift;
    }

    /**
     * @return UserInterface
     */
    public function getCreator()
    {
        return $this->creator;
    }
}
