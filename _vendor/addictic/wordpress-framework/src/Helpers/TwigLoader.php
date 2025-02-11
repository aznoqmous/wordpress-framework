<?php

namespace Addictic\WordpressFramework\Helpers;

use Addictic\WordpressFramework\Twig\AppRuntime;
use Addictic\WordpressFramework\Twig\TranslationExtension;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TemplateWrapper;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigLoader
{
    private $folders = [];

    private $twig;

    public function __construct()
    {
        $this->addFolder(AssetsHelper::getProjectDir("/templates"));
        $this->addFolder(AssetsHelper::sanitizePath(__DIR__ . "/../../templates"));

        $loader = new FilesystemLoader($this->folders);

        $this->twig = new Environment($loader);

        $this->twig->addFilter(new TwigFilter('trans', function ($string) {
            return Container::get("translator")->trans($string);
        }));

        $this->twig->addFilter(new TwigFilter('basename', function ($string) {
            return basename($string);
        }));

        $this->twig->addFilter(new TwigFilter('dirname', function ($string) {
            return dirname($string);
        }));

        $this->twig->addFilter(new TwigFilter('isImage', function ($string) {
            return FileHelper::isImage($string);
        }));

        $this->twig->addFunction(new TwigFunction('dump', function () {
            dump(...func_get_args());
        }));
    }

    public function getTemplate($templateName): TemplateWrapper
    {
        return $this->twig->load($templateName);
    }

    public function render($template, $arrOptions)
    {
        return $this->getTemplate($template)->render($arrOptions);
    }

    public function renderTemplate($template, $arrOptions)
    {
        $instance = Container::get("twig");
        $templateWrapper = $instance->getTemplate($template);
        do_action("wp_before_load_template", $template);
        echo $templateWrapper->render($arrOptions);
        do_action("wp_after_load_template", $template);
    }

    private function addFolder($path)
    {
        if (is_dir($path)) $this->folders[] = $path;
    }
}