<?php

namespace Addictic\WordpressFrameworkBundle\Helpers;

final class Config
{
    public static function get($key)
    {
        if (!($option = get_option($key))) return null;
        return $option;
    }

    public static function getJson($key)
    {
        $value = self::get($key);
        return json_decode(stripslashes($value));
    }
}