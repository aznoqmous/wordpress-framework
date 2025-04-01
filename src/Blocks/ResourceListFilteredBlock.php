<?php

namespace App\Blocks;

use Addictic\WordpressFramework\Annotation\Block;
use Addictic\WordpressFramework\Blocks\AbstractBlock;
use App\Models\ResourceCategoryModel;
use App\Models\ResourceModel;
use App\Models\ResourceTagModel;

/**
 * @Block(name="app/resource-list-filtered", template="blocks/resource-list-filtered.twig")
 * @property $categories
 * @property $tags
 * @property $resources
 */
class ResourceListFilteredBlock extends AbstractBlock
{

    public function compile($block_attributes, $content)
    {
        $this->categories = ResourceCategoryModel::findAll()->map(fn($el) => $el->row());
        $this->tags = ResourceTagModel::findAll()->map(fn($el) => $el->row());
        $this->resources = ResourceModel::findAll(["limit" => 12])->getModels();
    }
}