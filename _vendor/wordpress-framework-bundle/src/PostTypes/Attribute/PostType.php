<?php

namespace Addictic\WordpressFrameworkBundle\PostTypes\Attribute;


#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS)]
class PostType
{
    public $name;

    public function __construct(
        $name,
        public string $icon,
        public array $taxonomies=[],
        public int $priority = 0
    )
    {
        $this->name = $name;
    }
}