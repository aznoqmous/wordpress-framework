<?php

namespace Addictic\WordpressFramework;

use Addictic\WordpressFramework\Annotation\BlockManager;
use Addictic\WordpressFramework\Annotation\PostTypeManager;
use Addictic\WordpressFramework\Annotation\RouteManager;
use Addictic\WordpressFramework\Annotation\TaxonomyManager;

class WordpressFrameworkBundle
{
    public static function init()
    {
        $routeManager = new RouteManager();
        $routeManager->getRoutes();

        $taxonomyManager = new TaxonomyManager();
        $taxonomyManager->getTaxonomies();

        $entityManager = new PostTypeManager();
        $entityManager->getPostTypes();

        $blocksManager = new BlockManager();
        $blocksManager->getBlocks();
    }
}