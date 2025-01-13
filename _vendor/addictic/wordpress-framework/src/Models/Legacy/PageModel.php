<?php

namespace Addictic\WordpressFramework\Models\Legacy;

use Addictic\WordpressFramework\Helpers\WpmlHelper;
use Addictic\WordpressFramework\Models\AbstractPostTypeModel;

class PageModel extends AbstractPostTypeModel
{
    protected static $strName = "page";

    public function getFrontendUrl()
    {
        $url = $this->post_name;
        if($this->language_code != WpmlHelper::getDefaultLanguage()) $url = "$this->language_code/$url";
        return "/$url";
    }
}