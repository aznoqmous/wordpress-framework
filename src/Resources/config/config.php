<?php

use Addictic\WordpressFramework\Helpers\ViteHelper;
use Addictic\WordpressFramework\WordpressFrameworkBundle;

if (is_admin()) {
    ViteHelper::add("backend");
} else {
    ViteHelper::add("frontend");
}

WordpressFrameworkBundle::init();