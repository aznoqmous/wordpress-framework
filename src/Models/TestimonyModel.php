<?php

namespace App\Models;

use Addictic\WordpressFramework\Helpers\Container;
use Addictic\WordpressFramework\Models\AbstractPostTypeModel;
use Addictic\WordpressFramework\Models\Legacy\AttachmentModel;

class TestimonyModel extends AbstractPostTypeModel
{
    public static $strName = "testimony";

    public function renderItem()
    {
        $this->loadFields();
        return Container::get("twig")->render("parts/testimony-item.twig", array_merge($this->row(), [
            'logo' => $this->getLogoPath(),
            'href' => $this->getFrontendUrl()
        ]));
    }

    public function getLogoPath(){
        $this->loadFields();
        $file = AttachmentModel::findById($this->logo);
        return $file->guid;
    }
}