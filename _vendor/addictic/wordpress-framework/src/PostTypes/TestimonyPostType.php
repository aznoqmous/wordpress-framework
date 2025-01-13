<?php

namespace Addictic\WordpressFramework\PostTypes;

use Addictic\WordpressFramework\Annotation\PostType;
use Addictic\WordpressFramework\Fields\Framework\Field;
use Addictic\WordpressFramework\Fields\Framework\UploadField;

/**
 * @PostType(name="testimony", icon="dashicons-thumbs-up", priority=1)
 */
class TestimonyPostType extends AbstractPostType
{
    protected function configure()
    {
        $this->postType->options([
            'show_in_rest' => true
        ]);

        $this
            ->addMetabox("content_legend")
            ->addField(new Field("names", ['required' => true]))
            ->addField(new Field("function", ['required' => true]))
            ->addField(new UploadField("image"))
            ->apply()
        ;
    }

}