<?php

namespace Addictic\WordpressFramework\Blocks;

use Addictic\WordpressFramework\Annotation\Block;
use Addictic\WordpressFramework\Helpers\Container;
use Addictic\WordpressFramework\Helpers\TwigLoader;
use Addictic\WordpressFramework\Models\Legacy\PostModel;

/**
 * @Block(name="ifc/news-filtered-list", template="blocks/news-filtered-list.twig")
 */
class NewsFilteredListBlock extends AbstractBlock
{
    public function compile($block_attributes, $content)
    {
        $this->attributes = [
            'data-limit' => $block_attributes['newsCount'] ?? 3,
            'data-offset' => 0
        ];
        $this->categories = get_categories();

        $this->posts = PostModel::findAll([
            'limit' => $block_attributes['newsCount'] ?? 3,
            'order' => 'post_date DESC'
        ]);
    }
}