<?php

namespace BestWishes\Event;

use BestWishes\Entity\Category;
use Symfony\Contracts\EventDispatcher\Event;

class CategoryDeletedEvent extends Event
{
    public const NAME = 'category.deleted';

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
