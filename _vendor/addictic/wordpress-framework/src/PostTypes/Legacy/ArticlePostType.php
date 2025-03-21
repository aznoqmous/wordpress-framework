<?php

namespace Addictic\WordpressFramework\PostTypes\Legacy;

use Addictic\WordpressFramework\Annotation\PostType;
use Addictic\WordpressFramework\PostTypes\AbstractPostType;

/**
 * @PostType(name="post")
 */
class ArticlePostType extends AbstractPostType
{
    protected function configure()
    {
        $this->addSEO();
        $this->addDuplicateAction();
    }
}