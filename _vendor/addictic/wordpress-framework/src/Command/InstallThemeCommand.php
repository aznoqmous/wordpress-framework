<?php

namespace Addictic\WordpressFramework\Command;

use Addictic\WordpressFramework\Annotation\Command;
use Addictic\WordpressFramework\Helpers\AssetsHelper;

/**
 * @Command(name="install:theme")
 */
class InstallThemeCommand
{

    public function __invoke()
    {
        self::install();
    }

    public static function install()
    {
        self::symlink("vendor/addictic/wordpress-framework/public", "web/framework");
        self::symlink("src/Resources/theme", "web/app/themes/theme");
        self::copy("vendor/addictic/wordpress-framework/templates/console/console", "vendor/bin/console");
        self::copy("vendor/addictic/wordpress-framework/templates/console/console.bat", "vendor/bin/console.bat");
    }

    public static function symlink($from, $to): bool
    {
        $file = basename($to);
        $to = dirname(__DIR__, 5) . "/" . dirname($to);

        $to .= "/$file";

        $from = dirname(__DIR__, 5) . "/" . $from;

        $to = AssetsHelper::sanitizePath($to);
        $from = AssetsHelper::sanitizePath($from);

        if (file_exists($to) and !(is_file($to) or is_dir($to))) unlink($to);
        if (is_dir($to)) return false;

        echo "Creating symlink $to -> $from\n";
        return symlink($from, $to);
    }

    public static function copy($from, $to):bool
    {
        $file = basename($to);
        $to = dirname(__DIR__, 5) . "/" . dirname($to);

        $to .= "/$file";

        return copy($from, $to);
    }
}

