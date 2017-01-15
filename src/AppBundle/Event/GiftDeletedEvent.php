<?php

namespace AppBundle\Event;

use AppBundle\Entity\Gift;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\User\UserInterface;

class GiftDeletedEvent extends Event
{
    const NAME = 'gift.deleted';

    /**
     * @var Gift
     */
    protected $gift;

    /**
     * @var UserInterface
     */
    private $deleter;

    public function __construct(Gift $gift, UserInterface $deleter)
    {
        $this->gift = $gift;
        $this->deleter = $deleter;
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
    public function getDeleter()
    {
        return $this->deleter;
    }
}