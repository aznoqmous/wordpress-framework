<?php

namespace Addictic\WordpressFramework\Helpers;

class FileHelper
{
    public static function getExtension($path)
    {
        $path = explode(".", $path);
        return array_pop($path);
    }

    public static function isImage($path)
    {
        $extension = self::getExtension($path);
        $imageExtensions = ["gif", "jpg", "jpeg", "webp", "png"];
        $pattern = implode("|", $imageExtensions);
        return preg_match("/$pattern/", $extension);
    }
}