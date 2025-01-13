<?php

namespace Addictic\WordpressFramework\Taxonomies;

use Addictic\WordpressFramework\Annotation\Taxonomy;
use Addictic\WordpressFramework\Fields\Framework\Field;

/**
 * @Taxonomy(name="faqCategory")
 */
class FaqCategoryTaxonomy extends AbstractTaxonomy
{
    protected function configure()
    {
        $this->taxonomy->options(["show_in_rest" => true]);
    }
}