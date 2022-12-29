<?php

namespace BestWishes\Event;

use BestWishes\Entity\Gift;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

class GiftEditedEvent extends Event
{
    final public const NAME = 'gift.edited';


    public function __construct(protected Gift $originGift, protected Gift $editedGift, private readonly UserInterface $editor)
    {
    }

    public function getOriginGift(): Gift
    {
        return $this->originGift;
    }

    public function getEditedGift(): Gift
    {
        return $this->editedGift;
    }

    public function getEditor(): UserInterface
    {
        return $this->editor;
    }
}
