<?php

namespace App\Models;

use Addictic\WordpressFramework\Helpers\Container;
use Addictic\WordpressFramework\Models\AbstractPostTypeModel;
use Addictic\WordpressFramework\Models\AbstractTaxonomyModel;
use Addictic\WordpressFramework\Models\Legacy\AttachmentModel;
use App\Taxonomies\RealisationOptionTaxonomy;

class RealisationOptionTaxonomyModel extends AbstractTaxonomyModel
{
    public static $strName = "realisation_option";

    public function renderListItem()
    {
        return Container::get("twig")->render("parts/realisation-option-item.twig", $this->row());
    }
}