<?php

namespace Addictic\WordpressFramework\Models\Legacy;

use Addictic\WordpressFramework\Models\AbstractPostTypeModel;

class AttachmentModel extends AbstractPostTypeModel
{
    protected static $strName = "attachment";

    public function getFrontendUrl()
    {
        return wp_get_attachment_url($this->id);
    }
}