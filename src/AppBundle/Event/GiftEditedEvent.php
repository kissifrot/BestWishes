<?php

namespace AppBundle\Event;

use AppBundle\Entity\Gift;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\User\UserInterface;

class GiftEditedEvent extends Event
{
    const NAME = 'gift.edited';

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
    public function getOriginGift()
    {
        return $this->originGift;
    }

    /**
     * @return Gift
     */
    public function getEditedGift()
    {
        return $this->editedGift;
    }

    /**
     * @return UserInterface
     */
    public function getEditor()
    {
        return $this->editor;
    }
}
