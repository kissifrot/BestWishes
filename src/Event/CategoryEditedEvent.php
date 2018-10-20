<?php

namespace BestWishes\Event;

use BestWishes\Entity\Category;
use Symfony\Component\EventDispatcher\Event;

class CategoryEditedEvent extends Event
{
    public const NAME = 'category.edited';

    protected $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }
}
