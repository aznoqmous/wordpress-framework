<?php

namespace App\Blocks;

use Addictic\WordpressFramework\Annotation\Block;
use Addictic\WordpressFramework\Blocks\AbstractBlock;
use Addictic\WordpressFramework\Helpers\Config;
use App\Models\TestimonyModel;

/**
 * @Block(name="app/testimony-list", template="blocks/testimony.twig")
 */
class TestimonyListBlock extends AbstractBlock
{

    public function compile($block_attributes, $content)
    {
        $this->items = TestimonyModel::findAll(['limit' => 3]);
    }
}