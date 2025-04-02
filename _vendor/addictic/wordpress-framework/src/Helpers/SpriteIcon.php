<?php


namespace Addictic\WordpressFramework\Helpers;


use DOMDocument;
use DOMElement;

class SpriteIcon
{
    protected $name;
    protected $spriteFile;
    protected $localSpriteFile;

    protected string $iconsDir = "icons";

    protected DOMDocument $document;
    protected DOMElement $svgContainer;

    private function __construct($name)
    {
        $this->name = $name;
        $this->spriteFile = "{$this->iconsDir}/{$name}.svg";
        $this->localSpriteFile = AssetsHelper::getPublicDir($this->spriteFile);
        $this->document = new DOMDocument();
        $this->loadSpriteFile();
    }

    public static function create($name = "icons"): self
    {
        return new self($name);
    }

    private function loadSpriteFile()
    {
        $this->createFile();

        $spriteContent = file_get_contents($this->localSpriteFile);
        libxml_use_internal_errors(true);

        $this->document->loadHTML($spriteContent);
        $this->svgContainer = $this->document->getElementsByTagName("svg")[0];
    }

    private function createFile()
    {
        $iconDir = AssetsHelper::getPublicDir($this->iconsDir);
        if (!is_dir($iconDir)) mkdir($iconDir);

        if (!is_file($this->localSpriteFile) || !file_get_contents($this->localSpriteFile)) {
            file_put_contents($this->localSpriteFile, Container::get("twig")->render("backend/svg_sprite_file_template.twig"));
        }
    }

    public function getSpriteFile()
    {
        return "/{$this->spriteFile}";
    }

    public function getIcons()
    {
        $icons = [];
        $svgs = $this->document->getElementsByTagName("symbol");
        foreach ($svgs as $svg) {
            if ($svg->attributes) {
                $id = $svg->attributes->getNamedItem('id')->value;
                $icons[] = $id;
            }
        }
        return $icons;
    }

    public function addHTML($html)
    {
        $document = new DOMDocument();
        libxml_use_internal_errors(true);
        $document->loadHTML(stripslashes($html));

        $svgs = $document->getElementsByTagName("svg");
        foreach ($svgs as $svg) $this->addIcon($svg);
    }

    public function addSvg($svg)
    {
        $document = new DOMDocument();
        libxml_use_internal_errors(true);
        $document->loadHTML(stripslashes($svg));
        $svgElement = $document->getElementsByTagName("svg")[0];
        $this->addIcon($svgElement);
    }

    public function addIcon(DOMElement $svgElement)
    {
        $element = new DOMElement("symbol");
        $this->svgContainer->appendChild($element);

        if (!$id = $svgElement->attributes->getNamedItem('id')) return;
        if ($this->hasIcon($id->value)) $this->removeIcon($id->value);
        foreach ($svgElement->attributes as $attribute) {
            if ($attribute->name == "xmlns") continue;
            if ($attribute->name == "width") continue;
            if ($attribute->name == "height") continue;
            $element->setAttribute($attribute->name, $attribute->value);
        }

        foreach ($svgElement->getElementsByTagName("path") as $path) {
            $this->replaceColorAttribute($path, "stroke");
            $this->replaceColorAttribute($path, "fill");
        }
        foreach ($svgElement->getElementsByTagName("circle") as $path) {
            $this->replaceColorAttribute($path, "stroke");
            $this->replaceColorAttribute($path, "fill");
        }

        foreach ($svgElement->childNodes as $childNode) {
            $node = $this->svgContainer->ownerDocument->importNode($childNode, true);
            $element->appendChild($node);
        }

    }

    public function replaceColorAttribute($element, $attributeName)
    {
        if (!$attribute = $element->attributes->getNamedItem($attributeName)) return;
        if ($attribute->value == "black" || $attribute->value == "#000" || $attribute->value == "#000000") {
            $attribute->value = "currentColor";
        }
    }

    public function saveDocument()
    {
        $content = self::cleanSvg($this->document->saveHTML($this->svgContainer));
        $content = preg_replace("/viewbox/", "viewBox", $content);
        file_put_contents($this->localSpriteFile, $content);
    }

    public function hasIcon($id)
    {
        return $this->document->getElementById($id);
    }

    public function getIcon($id)
    {
        $icon = $this->document->getElementById($id);
        if (!$icon) return $id;
        $icon->setAttribute("class", "sprite-icon $id");
        $icon->setAttribute("xmlns", "http://www.w3.org/2000/svg");
        return preg_replace("/symbol/", "svg", self::cleanSvg($icon->ownerDocument->saveHTML($icon)));
    }

    public function removeIcon($id)
    {
        $element = $this->document->getElementById($id);
        $element->remove();
    }

    public static function cleanSvg($svgContent)
    {

        // remove decorator
        $svgContent = preg_replace("/<\?xml[^>]*?>/ms", "", $svgContent);

        // remove comments
        $svgContent = preg_replace("/<!--.*?-->/ms", '', $svgContent);

        // remove empty lines
        $svgContent = preg_replace("/\R\R/ms", PHP_EOL, $svgContent);

        return $svgContent;
    }
}