<?php

namespace Addictic\WordpressFrameworkBundle\Helpers;

class RoutingHelper
{
    static function redirect($url)
    {
        header("Location: $url");
        exit;
    }

    static function getCurrentUrl()
    {
        return parse_url($_SERVER['REQUEST_URI'])['url'];
    }

    static function getHost()
    {
        $protocol = $_SERVER['HTTPS'] == "on" ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        return $protocol . $host;
    }

    static function getAdminBase()
    {
        return self::getHost() . "/wp/wp-admin";
    }
}