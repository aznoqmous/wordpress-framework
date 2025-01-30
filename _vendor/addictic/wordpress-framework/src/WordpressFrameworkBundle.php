<?php

namespace Addictic\WordpressFramework;

use Addictic\WordpressFramework\Annotation\OldBlockManager;
use Addictic\WordpressFramework\Annotation\OldPostTypeManager;
use Addictic\WordpressFramework\Annotation\PostType;
use Addictic\WordpressFramework\Annotation\PostTypeManager;
use Addictic\WordpressFramework\Annotation\OldRouteManager;
use Addictic\WordpressFramework\Annotation\Route;
use Addictic\WordpressFramework\Annotation\RouteManager;
use Addictic\WordpressFramework\Annotation\TaxonomyManager;
use Addictic\WordpressFramework\Helpers\AssetsHelper;

class WordpressFrameworkBundle
{
    private function __construct()
    {

    }

    public static function init()
    {
        $instance = new static();
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
            );
        RouteManager::getInstance()
            ->discover(
                "\\Addictic\\WordpressFramework\\Controller",
                __DIR__ . "/Controller",
                Route::class
            );
//
//        $blocksManager = new BlockManager();
//        $blocksManager->getBlocks();

        $instance->registerAssets();
    }

    public function registerAssets()
    {
//        AssetsHelper::registerScript("/framework/backend.js");
//        AssetsHelper::registerStyle("/framework/backend.css");
    }
}