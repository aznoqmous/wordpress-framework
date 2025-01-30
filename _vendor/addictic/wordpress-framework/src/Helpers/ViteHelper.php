<?php

namespace Addictic\WordpressFramework\Helpers;

// https://github.com/andrefelipe/vite-php-setup

use Contao\CoreBundle\ServiceAnnotation\Hook;

class ViteHelper
{
    private const VITE_HOST = 'http://localhost:5133';
    static $cssEntries = [];

    public static function add($entry = "main")
    {
        $entry .= ".js";
        $entries = self::vite($entry);
        add_action("wp_head", function () use ($entries) {
            echo implode(PHP_EOL, $entries ?: []);
        });

        add_action("admin_head", function () use ($entries) {
            echo implode(PHP_EOL, $entries ?: []);
        });

        add_action("wp_loaded", function () use ($entries) {
            add_action("wp_print_scripts", function () use ($entries) {
                echo implode(PHP_EOL, $entries ?: []);
            });
        });
    }

    private static function vite(string $entry = "main.js"): array
    {
        return [
            self::jsTag($entry)
            , self::jsPreloadImports($entry)
            , self::cssTag($entry)
        ];
    }


    // Some dev/prod mechanism would exist in your project
    private static function isDev(string $entry): bool
    {
        // This method is very useful for the local server
        // if we try to access it, and by any means, didn't started Vite yet
        // it will fallback to load the production files from manifest
        // so you still navigate your site as you intended!
        if(!$_SERVER || !isset($_SERVER['HTTP_HOST'])) return false;
        return preg_match("/\.test/", $_SERVER['HTTP_HOST']);
    }


    // Helpers to print tags
    private static function jsTag(string $entry): string
    {
        $url = self::isDev($entry)
            ? self::VITE_HOST . '/' . $entry
            : self::assetUrl($entry);
        return '<script type="module" crossorigin src="'
            . $url
            . '"></script>';
    }

    private static function jsPreloadImports(string $entry): string
    {
        if (self::isDev($entry)) {
            return '';
        }

        $res = '';
        foreach (self::importsUrls($entry) as $url) {
            $res .= '<link rel="modulepreload" href="'
                . $url
                . '">';
        }
        return $res;
    }

    private static function cssTag(string $entry): string
    {
        // not needed on dev, it's inject by Vite
        if (self::isDev($entry)) {
            return '';
        }

        $tags = '';
        foreach (self::cssUrls($entry) as $url) {
            self::$cssEntries[] = $url;
            $tags .= '<link rel="stylesheet" href="'
                . $url
                . '">';
        }
        return $tags;
    }

    private static function getManifest()
    {
        $path = AssetsHelper::toLocalPath("/dist/.vite/manifest.json");
        $content = file_get_contents($path);
        return json_decode($content, true);
    }

    private static function assetUrl(string $entry): string
    {
        $manifest = self::getManifest();

        return isset($manifest[$entry])
            ? '/dist/' . $manifest[$entry]['file']
            : '';
    }

    private static function importsUrls(string $entry): array
    {
        $urls = [];
        $manifest = self::getManifest();

        if (!empty($manifest[$entry]['imports'])) {
            foreach ($manifest[$entry]['imports'] as $imports) {
                $urls[] = '/dist/' . $manifest[$imports]['file'];
            }
        }
        return $urls;
    }

    private static function cssUrls(string $entry): array
    {
        $urls = [];
        $manifest = self::getManifest();

        if (!empty($manifest[$entry]['css'])) {
            foreach ($manifest[$entry]['css'] as $file) {
                $urls[] = '/dist/' . $file;
            }
        }
        return $urls;
    }
}