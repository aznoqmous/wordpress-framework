<?php

namespace Addictic\WordpressFramework\Models;

use Addictic\WordpressFramework\Helpers\Container;
use Addictic\WordpressFramework\Helpers\StringHelper;

class TestimonyModel extends AbstractPostTypeModel
{
    protected static $strName = "testimony";

    public function renderItem()
    {
        $this->loadFields();
        return Container::get("twig")->render("testimony/item.twig", array_merge($this->row(), [
            'image' => wp_get_attachment_url($this->image),
            'content' => $this->parseContent()
        ]));
    }
}