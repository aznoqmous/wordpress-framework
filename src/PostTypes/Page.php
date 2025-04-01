<?php

namespace App\PostTypes;

use Addictic\WordpressFramework\Annotation\PostType;
use Addictic\WordpressFramework\Fields\ColorField;
use Addictic\WordpressFramework\PostTypes\Legacy\PagePostType;

/**
 * @PostType(name="page")
 */
class Page extends PagePostType
{
    protected function configure()
    {
        parent::configure();

        $this->addMetabox("layout_legend")
            ->addField(new ColorField("background_color", [
                'colors' => [
                    "white",
                    "#06345D"
                ]
            ]))
            ->addField(new ColorField("text_color", [
                'colors' => [
                    "black",
                    "white"
                ]
            ]))
            ->apply();
    }
}