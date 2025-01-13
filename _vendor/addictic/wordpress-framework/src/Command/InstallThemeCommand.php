<?php

namespace Addictic\WordpressFramework\Command;

use Addictic\WordpressFramework\Helpers\AssetsHelper;

class InstallThemeCommand
{
    public static function install()
    {
        self::symlink('src/Resources/theme', 'web/app/themes/theme');
    }

    public static function symlink($from, $to): bool
    {
        $file = basename($to);
        $to = dirname(__DIR__, 5) . "/" . dirname($to);

        $to .= "/$file";

        $from = dirname(__DIR__, 5) . "/" . $from;

        $to = AssetsHelper::sanitizePath($to);
        $from = AssetsHelper::sanitizePath($from);

        if (!(is_file($to) or is_dir($to))) unlink($to);
        if (is_dir($to)) return false;

        echo "Creating symlink $to -> $from";
        return symlink($from, $to);
    }
}

