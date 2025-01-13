<?php

namespace Addictic\WordpressFramework\Blocks;

use Addictic\WordpressFramework\Annotation\Block;
use Addictic\WordpressFramework\Models\FaqCategoryTaxonomyModel;
use Addictic\WordpressFramework\Models\FaqModel;
use Addictic\WordpressFramework\PostTypes\FaqPostType;

/**
 * @Block(name="ifc/faq", template="blocks/faq.twig")
 */
class FaqBlock extends AbstractBlock
{
    public function compile($block_attributes, $content)
    {
        $categories = [];
        foreach (FaqCategoryTaxonomyModel::findAll() as $category) {
            $categories[] = array_merge([
                'posts' => FaqModel::findByTaxonomies([$category->id]),
            ], $category->row());
        }
        $this->categories = $categories;
    }
}