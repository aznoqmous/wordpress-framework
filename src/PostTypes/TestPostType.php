<?php

namespace App\PostTypes;

use Addictic\WordpressFramework\Annotation\PostType;
use Addictic\WordpressFramework\PostTypes\AbstractPostType;

/**
 * @PostType(name="test")
 */
class TestPostType extends AbstractPostType
{
    protected function configure()
    {
        $this->addSEO();
        $this->addDuplicateAction();
    }
}