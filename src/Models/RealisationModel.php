<?php

namespace App\Models;

use Addictic\WordpressFramework\Helpers\Container;
use Addictic\WordpressFramework\Models\AbstractPostTypeModel;
use Addictic\WordpressFramework\Models\Legacy\AttachmentModel;
use App\Taxonomies\RealisationOption;

class RealisationModel extends AbstractPostTypeModel
{
    public static $strName = "realisation";

    public function renderItem()
    {
        $this->loadFields();
        $images = $this->getImages();
        return Container::get("twig")->render("parts/realisation-item.twig", array_merge([
            "main_image" => $images ? $images->current()->guid : null,
            "href" => $this->getFrontendUrl(),
            "badges" => get_the_terms($this->id, "realisation_option")
        ], $this->row()));
    }

    public function getImages()
    {
        $ids = array_filter(explode(",", $this->getValue("images") ?: ""), fn($value) => $value);
        return count($ids) ? AttachmentModel::findByIds($ids) : null;
    }

    public function getOptions()
    {
        $jobs = get_the_terms(intval($this->id), "realisation_option");
        $ids = array_map(fn($el) => $el->term_id, is_array($jobs) ? $jobs : []);
        return count($ids) ? RealisationOptionTaxonomyModel::findByIds($ids) : null;
    }
}