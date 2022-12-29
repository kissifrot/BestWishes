<?php

namespace BestWishes\Event;

use BestWishes\Entity\Category;
use Symfony\Contracts\EventDispatcher\Event;

class CategoryEditedEvent extends Event
{
    final public const NAME = 'category.edited';

    public function __construct(protected Category $category)
    {
    }

    public function getCategory(): Category
    {
        return $this->category;
    }
}
