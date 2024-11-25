<?php

namespace Addictic\WordpressFrameworkBundle\Models\Legacy;

use Addictic\WordpressFrameworkBundle\Models\AbstractPostTypeModel;

class AttachmentModel extends AbstractPostTypeModel
{
    protected static $strName = "attachment";

    public function getFrontendUrl()
    {
        return wp_get_attachment_url($this->id);
    }
}