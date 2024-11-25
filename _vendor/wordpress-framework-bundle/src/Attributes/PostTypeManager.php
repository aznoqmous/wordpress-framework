<?php

namespace Addictic\WordpressFrameworkBundle\Attributes;

use Addictic\WordpressFrameworkBundle\PostTypes\Attribute\PostType;

class PostTypeManager extends AbstractManager
{
    protected static $attribute = PostType::class;

    public function create($instance, $attribute)
    {
        $instance->name = $attribute->name;
        $instance->icon = $attribute->icon ?? $instance->icon;
        $instance->taxonomies = $attribute->taxonomies ?? $instance->taxonomies;
        $instance->priority = $attribute->priority ?? $instance->priority;
    }

    public function register()
    {
        uasort($this->instances, fn($a,$b)=> $a->priority - $b->priority);
        foreach ($this->instances as $instance){
            $instance->register();
        }
    }
}