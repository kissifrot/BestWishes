<?php

namespace BestWishes\Event;

use BestWishes\Entity\Category;
use Symfony\Contracts\EventDispatcher\Event;

class CategoryDeletedEvent extends Event
{
    final public const NAME = 'category.deleted';

    public function __construct(protected Category $category)
    {
    }

    public function getCategory(): Category
    {
        return $this->category;
    }
}
