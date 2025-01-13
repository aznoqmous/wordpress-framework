<?php

namespace Addictic\WordpressFramework\Annotation;

/**
 * @Annotation
 */
class PostType
{
    public $name;
    public $icon;
    public $taxonomies;
    public $priority = 0;
}