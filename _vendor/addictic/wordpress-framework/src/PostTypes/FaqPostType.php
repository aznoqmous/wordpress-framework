<?php

namespace Addictic\WordpressFramework\PostTypes;

use Addictic\WordpressFramework\Annotation\PostType;

/**
 * @PostType(name="faq", icon="dashicons-admin-comments", taxonomies="faqCategory", priority=1)
 */
class FaqPostType extends AbstractPostType
{
    protected function configure()
    {
        $this->postType->options([
            'show_in_rest' => true,
        ]);
    }
}