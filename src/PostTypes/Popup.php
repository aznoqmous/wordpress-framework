<?php

namespace App\PostTypes;

use Addictic\WordpressFramework\Annotation\PostType;
use Addictic\WordpressFramework\Fields\CheckboxField;
use Addictic\WordpressFramework\Fields\ColorField;
use Addictic\WordpressFramework\Fields\DateField;
use Addictic\WordpressFramework\Fields\PageField;
use Addictic\WordpressFramework\Fields\UploadField;
use Addictic\WordpressFramework\PostTypes\AbstractPostType;

/**
 * @PostType(name="popup", icon="dashicons-pressthis")
 */
class Popup extends AbstractPostType
{

    protected function configure()
    {
        $this->postType->options([
            'show_in_rest' => true,
            'supports' => array('title', 'editor', 'thumbnail'),
        ]);

        $this
            ->addColumn("title")
            ->addColumn("start_date")
            ->addColumn("end_date")
        ;
        $this->columns->set($this->columns->add);

        $this
            ->addMetabox("content_legend")
                ->addField(new UploadField("image", ['multiple' => false, 'fileType' => "image/*"]))
                ->addField(new DateField("start_date"))
                ->addField(new DateField("end_date"))
                ->addField(new PageField("included_pages"))
                ->addField(new PageField("excluded_pages"))
                ->addField(new CheckboxField("display_once"))
                ->addField(new ColorField("color", [
                    'colors' => [
                        null,
                        "#36A9E1",
                        "#06345D",
                    ]
                ]))
            ->apply()
        ;

    }
}