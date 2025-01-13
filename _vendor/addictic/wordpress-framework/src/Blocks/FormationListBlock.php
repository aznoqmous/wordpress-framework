<?php

namespace Addictic\WordpressFramework\Blocks;

use Addictic\WordpressFramework\Annotation\Block;
use Addictic\WordpressFramework\Models\FaqCategoryTaxonomyModel;
use Addictic\WordpressFramework\Models\FaqModel;
use Addictic\WordpressFramework\Models\FormationModel;

/**
 * @Block(name="ifc/formation-list", template="blocks/formation-list.twig")
 */
class FormationListBlock extends AbstractBlock
{
    public function compile($block_attributes, $content)
    {
        $formationIds = array_map(fn($el)=> $el['id'], isset($block_attributes['formations']) ? $block_attributes['formations']: []);
        $this->title = isset($block_attributes['title']) ? $block_attributes['title'] : "";
        $this->formations = FormationModel::findByIds($formationIds) ?: [];
    }
}