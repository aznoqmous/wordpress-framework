<?php

namespace App\PostTypes;

use Addictic\WordpressFramework\Annotation\PostType;
use Addictic\WordpressFramework\Fields\InputField;
use Addictic\WordpressFramework\Fields\TextareaField;
use Addictic\WordpressFramework\Fields\UploadField;
use Addictic\WordpressFramework\Helpers\Container;
use Addictic\WordpressFramework\PostTypes\AbstractPostType;

/**
 * @PostType(name="resource", icon="dashicons-media-archive", taxonomies="resource_category,resource_tag")
 */
class Resource extends AbstractPostType
{
    protected function configure()
    {
        $this->postType->options([
            'show_in_rest' => true,
            'supports' => array('title', 'editor', 'thumbnail', 'taxonomies'),
        ]);

        $this->addSEO();
        $this->addDuplicateAction();
    }
}