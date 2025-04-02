<?php

namespace Addictic\WordpressFramework\Helpers;

class AssetsHelper
{
    public static function registerScript($path, $module = false)
    {
        $path = self::toAbsolutePath($path);
        add_action("wp_head", function () use ($path, $module) {
            $module = $module ? ' type="module"' : "";
            echo "<script src='$path'$module></script>";
        });
    }

    public static function registerStyle($path)
    {
        $path = self::toAbsolutePath($path);
        add_action("wp_enqueue_scripts", function () use ($path) {
            wp_enqueue_style($path, $path);
        });
    }

    public static function sanitizePath($path)
    {
        $path = preg_replace("/\/+/", DIRECTORY_SEPARATOR, $path);
        $path = preg_replace("/\\+/", DIRECTORY_SEPARATOR, $path);

        $result = [];
        foreach (explode(DIRECTORY_SEPARATOR, $path) as $part) {
            if ($part == "..") {
                array_splice($result, -1, 1);
                continue;
            }
            if ($part == ".") continue;
            $result[] = $part;
        }
        $path = implode(DIRECTORY_SEPARATOR, $result);

        return $path;
    }

    public static function toAbsolutePath($path)
    {
        return RoutingHelper::getHost() . "/" . preg_replace("/^\//", "", $path);
    }

    public static function toLocalPath($path)
    {
        $absPath = str_replace(DIRECTORY_SEPARATOR . "wp/", "", ABSPATH);
        $absPath = self::sanitizePath($absPath . parse_url($path)['path']);
        return $absPath;
    }

    public static function getProjectDir($appendPath = "")
    {
        return self::sanitizePath(ABSPATH . "/../../$appendPath");
    }

    public static function getPublicDir($appendPath = "")
    {
        return self::getProjectDir("/web/$appendPath");
    }

    public static function getFrameworkDir($appendPath = "")
    {
        return self::getPublicDir("/framework/$appendPath");
    }

    public static function path(...$args)
    {
        return self::sanitizePath(implode(DIRECTORY_SEPARATOR, $args));
    }
}