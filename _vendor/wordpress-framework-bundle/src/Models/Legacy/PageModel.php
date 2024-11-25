<?php

namespace Addictic\WordpressFrameworkBundle\Models\Legacy;

use Addictic\WordpressFrameworkBundle\Models\AbstractPostTypeModel;

class PageModel extends AbstractPostTypeModel
{
    protected static $strName = "page";

    public function getFrontendUrl()
    {
        return "/" . $this->post_name;
    }
}