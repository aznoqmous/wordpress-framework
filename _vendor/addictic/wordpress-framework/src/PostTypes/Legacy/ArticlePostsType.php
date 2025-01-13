<?php

namespace Addictic\WordpressFramework\PostTypes\Legacy;

use Addictic\WordpressFramework\Annotation\PostType;
use Addictic\WordpressFramework\Fields\Framework\Field;
use Addictic\WordpressFramework\Fields\Framework\TextareaField;
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