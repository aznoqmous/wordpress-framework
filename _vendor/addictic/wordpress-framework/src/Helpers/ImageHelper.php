<?php

namespace Addictic\WordpressFramework\Helpers;

class ImageHelper
{
    public static function renderImageById($id, $size = "full", $icon = false)
    {
        return wp_get_attachment_image($id, $size, $icon);
    }

    public static function getImagePathById($id, $size = "full", $icon = false)
    {
        return wp_get_attachment_image_url($id, $size, $icon);
    }

    public static function renderImageByPath($path, $alt="")
    {
        return "<img src='$path' alt='$alt'>";
    }

    public static function renderThemeModImage($slug, $size = "full", $icon = false)
    {
        $imageId = get_theme_mod($slug);
        return $imageId ? self::renderImageById($imageId, $size, $icon) : null;
    }
}