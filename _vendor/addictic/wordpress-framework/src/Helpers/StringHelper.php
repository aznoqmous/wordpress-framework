<?php

namespace Addictic\WordpressFramework\Helpers;

class StringHelper
{
    public static function generateAlias($string)
    {
        $string = strtolower($string);
        $string = str_replace(" ", "-", $string);
        return $string;
    }

    public static function substr($string, $length = null)
    {
        if (strlen($string) > $length - 3) {
            $string = substr($string, 0, $length - 3) . "...";
        }
        return $string;
    }

    public static function parseBlock($content)
    {
        $blocks = parse_blocks($content);
        if (!is_array($blocks)) return "";

        $arrHtml = [];
        foreach ($blocks as $block) {
            $arrHtml[] = render_block($block);
        }

        return implode("", $arrHtml);
    }
}