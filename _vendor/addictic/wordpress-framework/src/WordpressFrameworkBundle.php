<?php

namespace Addictic\WordpressFramework;

use Addictic\WordpressFramework\Annotation\BlockManager;
use Addictic\WordpressFramework\Annotation\OldPostTypeManager;
use Addictic\WordpressFramework\Annotation\PostType;
use Addictic\WordpressFramework\Annotation\PostTypeManager;
use Addictic\WordpressFramework\Annotation\RouteManager;
use Addictic\WordpressFramework\Annotation\TaxonomyManager;

class WordpressFrameworkBundle
{
    public static function init()
    {
//        $routeManager = new RouteManager();
//        $routeManager->getRoutes();
//
//        $taxonomyManager = new TaxonomyManager();
//        $taxonomyManager->getTaxonomies();
//
        PostTypeManager::getInstance()
            ->discover(
                "\\Addictic\\WordpressFramework\\PostTypes",
                __DIR__ . "/PostTypes",
                PostType::class
            )
        ;
//
//        $blocksManager = new BlockManager();
//        $blocksManager->getBlocks();
    }
}