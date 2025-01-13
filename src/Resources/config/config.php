<?php

use Addictic\WordpressFramework\Helpers\ViteHelper;
use Addictic\WordpressFramework\WordpressFrameworkBundle;

if (is_admin()) {
    ViteHelper::add("backend");
} else {
    ViteHelper::add("frontend");
}

WordpressFrameworkBundle::init();

$discovery = \Addictic\WordpressFramework\Annotation\PostTypeManager::getInstance()
    ->discover(
        "\\App\\PostTypes",
        __DIR__ . "/../../PostTypes",
        \Addictic\WordpressFramework\Annotation\PostType::class
    )
;