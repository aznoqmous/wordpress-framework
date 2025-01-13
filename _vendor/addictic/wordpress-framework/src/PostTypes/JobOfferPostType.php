<?php

namespace Addictic\WordpressFramework\PostTypes;

use Addictic\WordpressFramework\Annotation\PostType;
use Addictic\WordpressFramework\Fields\Framework\TextareaField;

/**
 * @PostType(name="jobOffer", icon="dashicons-pressthis", priority=1)
 */
class JobOfferPostType extends AbstractPostType
{
    protected function configure()
    {
        $this->postType->options([
            'show_in_rest' => true
        ]);

        $this->addMetabox("content")
            ->addField(new TextareaField("description", [
                'editor' => "wp_editor"
            ]))
            ->apply()
        ;

        $this->addSEO();
    }
}