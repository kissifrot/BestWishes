<?php

namespace BestWishes\Event;

use BestWishes\Entity\Gift;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

class GiftEditedEvent extends Event
{
    public const NAME = 'gift.edited';

    /**
     * @var Gift
     */
    protected $originGift;

    /**
     * @var Gift
     */
    protected $editedGift;

    /**
     * @var UserInterface
     */
    private $editor;

    public function __construct(Gift $originGift, Gift $editedGift, UserInterface $editor)
    {
        $this->originGift = $originGift;
        $this->editedGift = $editedGift;
        $this->editor = $editor;
    }

    /**
     * @return Gift
     */
    public function getOriginGift(): Gift
    {
        return $this->originGift;
    }

    /**
     * @return Gift
     */
    public function getEditedGift(): Gift
    {
        return $this->editedGift;
    }

    /**
     * @return UserInterface
     */
    public function getEditor(): UserInterface
    {
        return $this->editor;
    }
}
