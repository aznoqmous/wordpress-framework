<?php

namespace App\PostTypes;

use Addictic\WordpressFramework\Annotation\PostType;
use Addictic\WordpressFramework\Fields\InputField;
use Addictic\WordpressFramework\Fields\TextareaField;
use Addictic\WordpressFramework\Fields\UploadField;
use Addictic\WordpressFramework\PostTypes\AbstractPostType;

/**
 * @PostType(name="testimony")
 */
class Testimony extends AbstractPostType
{
    protected function configure()
    {
        $this->postType->options([
            'show_in_rest' => true,
            'supports' => array('title', 'editor', 'thumbnail', 'taxonomies'),
        ]);

        $this
            ->addMetabox("content_legend")
                ->addField(new InputField("name"))
                ->addField(new InputField("job"))
                ->addField(new TextareaField("description", ["editor" => "wp_editor"]))
                ->addField(new UploadField("logo", [
                    'fileType' => "image",
                    'multiple' => false
                ]))
            ->apply()
        ;

        $this->addSEO();
        $this->addDuplicateAction();
    }
}