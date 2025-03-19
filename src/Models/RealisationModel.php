<?php

namespace App\Models;

use Addictic\WordpressFramework\Helpers\Container;
use Addictic\WordpressFramework\Models\AbstractPostTypeModel;
use Addictic\WordpressFramework\Models\Legacy\AttachmentModel;

class RealisationModel extends AbstractPostTypeModel
{
    public static $strName = "realisation";

    public function renderItem()
    {
        $images = $this->getImages();
        return Container::get("twig")->render("parts/realisation-item.twig", array_merge([
            "main_image" => $images ? $images->current()->guid : null,
            "href" => $this->getFrontendUrl()
        ], $this->row()));
    }

    public function getImages()
    {
        $ids = array_filter(explode(",", $this->getValue("images") ?: ""), fn($value)=> $value);
        return count($ids) ? AttachmentModel::findByIds($ids) : null;
    }
}