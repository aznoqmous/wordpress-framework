<?php

namespace Addictic\WordpressFramework\Helpers;

class AssetsHelper
{
    public static function registerScript($path)
    {
        add_action("wp_head", function () use ($path) {
            echo "<script src='$path'></script>";
        });
    }

    public static function registerStyle($path)
    {
        add_action("wp_enqueue_scripts", function () use ($path) {
            wp_enqueue_style($path, $path);
        });
    }

    public static function sanitizePath($path)
    {
        $path = preg_replace("/\/+/", DIRECTORY_SEPARATOR, $path);
        $path = preg_replace("/\\+/", DIRECTORY_SEPARATOR, $path);
        return $path;
    }

    public static function toAbsolutePath($path)
    {
        $absPath = str_replace(DIRECTORY_SEPARATOR . "wp/", "", ABSPATH);
        $absPath = self::sanitizePath($absPath . parse_url($path)['path']);
        return $absPath;
    }
}