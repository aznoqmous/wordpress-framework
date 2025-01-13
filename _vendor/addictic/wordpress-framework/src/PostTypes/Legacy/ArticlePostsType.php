<?php

namespace Addictic\WordpressFramework\PostTypes\Legacy;

use Addictic\WordpressFramework\PostTypes\AbstractPostType;

/**
 * @PostType(name="post")
 */
class ArticlePostsType extends AbstractPostType
{
    protected function configure()
    {
        $this->addSEO();
        $this->addDuplicateAction();
    }
}