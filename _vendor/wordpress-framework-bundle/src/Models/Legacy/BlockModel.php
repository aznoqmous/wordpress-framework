<?php

namespace Addictic\WordpressFrameworkBundle\Models\Legacy;

use Addictic\WordpressFrameworkBundle\Helpers\StringHelper;
use Addictic\WordpressFrameworkBundle\Models\AbstractPostTypeModel;

class BlockModel extends AbstractPostTypeModel
{
    protected static $strName = "wp_block";

    public function render()
    {
        return StringHelper::parseBlock($this->getPost()->post_content);
    }

    public static function renderById($id)
    {
        if (!$id) return "";
        $block = self::findById($id);
        return $block ? $block->render() : "";
    }

    public static function renderByIds($ids)
    {
        $ids = json_decode(stripslashes($ids));
        $blocks = self::findByIds($ids);
        return $blocks ? implode("", $blocks->map(fn($el) => $el->render())) : "";
    }
}