<?php

namespace Addictic\WordpressFramework\Blocks;

use Addictic\WordpressFramework\Annotation\Block;
use Addictic\WordpressFramework\Helpers\Container;
use Addictic\WordpressFramework\Helpers\TwigLoader;
use Addictic\WordpressFramework\Models\Legacy\PostModel;

/**
 * @Block(name="ifc/news-latest-list", template="blocks/news-latest-list.twig")
 */
class NewsLatestListBlock extends AbstractBlock
{
    public function compile($block_attributes, $content)
    {
        $postIds = get_option("sticky_posts");

        $featuredPosts = PostModel::findByIds($postIds, [
            'limit' => $block_attributes['newsCount'] ?? 3,
            'order' => 'post_date DESC'
        ]);

        $featuredPosts = $featuredPosts ? $featuredPosts->getModels() : [];

        $regularPosts = PostModel::findByExcludeIds([], $postIds, [
            'limit' => ($block_attributes['newsCount'] ?? 3) - count($featuredPosts),
            'order' => 'post_date DESC'
        ]);

        $regularPosts = $regularPosts ? $regularPosts->getModels() : [];

        $this->posts = [...$featuredPosts, ...$regularPosts];
    }
}