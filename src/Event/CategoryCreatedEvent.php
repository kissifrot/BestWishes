<?php

namespace BestWishes\Event;

use BestWishes\Entity\Category;
use Symfony\Contracts\EventDispatcher\Event;

class CategoryCreatedEvent extends Event
{
    final public const NAME = 'category.created';

    public function __construct(protected Category $category)
    {
    }

    public function getCategory(): Category
    {
        return $this->category;
    }
}
