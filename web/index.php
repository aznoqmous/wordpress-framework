<?php

use App\Kernel;
require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context): Kernel {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};


///**
// * WordPress View Bootstrapper
// */
//define('WP_USE_THEMES', true);
//require __DIR__ . '/wp/wp-blog-header.php';
