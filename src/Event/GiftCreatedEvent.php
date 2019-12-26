<?php

namespace BestWishes\Event;

use BestWishes\Entity\Gift;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

class GiftCreatedEvent extends Event
{
    public const NAME = 'gift.created';

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
    public function getGift(): Gift
    {
        return $this->gift;
    }

    /**
     * @return UserInterface
     */
    public function getCreator(): UserInterface
    {
        return $this->creator;
    }
}
