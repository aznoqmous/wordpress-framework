<?php

namespace App\Models;

use Addictic\WordpressFramework\Models\AbstractPostTypeModel;

class ResourceModel extends AbstractPostTypeModel
{
    public static $strName = "resource";

    public function getParent()
    {
        return self::findById($this->post_parent);
    }

    public function getChildren()
    {
        return self::findBy([
            "post_parent = \"{$this->id}\""
        ]);
    }
}