<?php

namespace Addictic\WordpressFramework;

use Addictic\WordpressFramework\Annotation\Block;
use Addictic\WordpressFramework\Annotation\BlockManager;
use Addictic\WordpressFramework\Annotation\Command;
use Addictic\WordpressFramework\Annotation\CommandManager;
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

        BlockManager::getInstance()
            ->discover("\\Addictic\\WordpressFramework\\Blocks",
                __DIR__ . "/Blocks",
                Block::class
            );

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

    }

    public static function initializeWordpress()
    {
        require_once AssetsHelper::getProjectDir("/web/wp/load.php");
    }


    public static function registerCommands()
    {
        CommandManager::getInstance()
            ->discover(
                "\\Addictic\\WordpressFramework\\Command",
                __DIR__ . "/Command",
                Command::class
            );
    }
}