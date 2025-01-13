<?php

namespace Addictic\WordpressFramework\Blocks;

use Addictic\WordpressFramework\Annotation\Block;
use Addictic\WordpressFramework\Helpers\Container;
use Addictic\WordpressFramework\Helpers\TwigLoader;
use Addictic\WordpressFramework\Models\Legacy\PostModel;
use Addictic\WordpressFramework\Models\TestimonyModel;

/**
 * @Block(name="ifc/testimony-list", template="blocks/testimony-list.twig")
 */
class TestimonyListBlock extends AbstractBlock
{
    public function compile($block_attributes, $content)
    {
        $this->items = TestimonyModel::findAll();
    }
}