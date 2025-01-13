<?php

namespace Addictic\WordpressFramework\Fields;

use Addictic\WordpressFramework\Fields\Framework\Field;
use Addictic\WordpressFramework\Helpers\AssetsHelper;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

class ModuleSelectionField extends Field
{
    protected string $strTemplate = "module";

    public function render()
    {
        $files = [];

        $dir = wp_upload_dir();

        $absoluteDir = AssetsHelper::sanitizePath($dir['basedir'] . '/modules/');
        $directory = new RecursiveDirectoryIterator($absoluteDir);
        $iterator = new RecursiveIteratorIterator($directory);
        $regex = new RegexIterator($iterator, '/^.+\.html$/i', RegexIterator::GET_MATCH);

        foreach ($regex as $match) {
            $f = new \SplFileInfo($match[0]);
            $path = $f->getPathname();
            $files[] = (object)[
                'name' => str_replace($absoluteDir, "", $path),
                'path' => str_replace("\\", "/", str_replace($absoluteDir, $dir['baseurl'] . "/modules/", $path))
            ];
        }

        $this->args['files'] = $files;

        return parent::render();

    }
}