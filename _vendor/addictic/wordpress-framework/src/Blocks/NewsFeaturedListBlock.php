<?php

namespace Addictic\WordpressFramework\Blocks;

use Addictic\WordpressFramework\Annotation\Block;
use Addictic\WordpressFramework\Helpers\Container;
use Addictic\WordpressFramework\Helpers\TwigLoader;
use Addictic\WordpressFramework\Models\Legacy\PostModel;

/**
 * @Block(name="ifc/news-featured-list", template="blocks/news-featured-list.twig")
 */
class NewsFeaturedListBlock extends AbstractBlock
{
    public function compile($block_attributes, $content)
    {
        $postIds = get_option("sticky_posts");

        $this->posts = PostModel::findByIds($postIds, [
            'limit' => $block_attributes['newsCount'] ?? 3,
            'order' => 'post_date DESC'
        ]);
    }
}