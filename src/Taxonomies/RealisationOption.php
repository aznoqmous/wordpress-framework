<?php

namespace App\Taxonomies;

use Addictic\WordpressFramework\Annotation\Taxonomy;
use Addictic\WordpressFramework\Blocks\AbstractBlock;
use Addictic\WordpressFramework\Fields\IconField;
use Addictic\WordpressFramework\Taxonomies\AbstractTaxonomy;

/**
 * @Taxonomy(name="realisation_option")
 */
class RealisationOption extends AbstractTaxonomy
{
    protected function configure()
    {
        $this->taxonomy->options(["show_in_rest" => true]);
    }
}