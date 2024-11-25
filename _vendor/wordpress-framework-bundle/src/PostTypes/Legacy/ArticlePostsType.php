<?php

namespace Addictic\WordpressFrameworkBundle\PostTypes\Legacy;

use Addictic\WordpressFrameworkBundle\Annotation\PostType;
use Addictic\WordpressFrameworkBundle\Fields\Framework\Field;
use Addictic\WordpressFrameworkBundle\Fields\Framework\TextareaField;
use Addictic\WordpressFrameworkBundle\PostTypes\AbstractPostType;

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