<?php

namespace Addictic\WordpressFramework\Fields;

use Addictic\WordpressFramework\Helpers\AssetsHelper;
use Addictic\WordpressFramework\Helpers\Container;
use Addictic\WordpressFramework\Helpers\SpriteIcon;
use DOMDocument;

/**
 * An input to create a named svg sprite
 */
class SVGSpriteField extends Field
{
    protected string $strTemplate = "svg_sprite_field";

    protected string $iconsDir = "icons";

    public function render()
    {
        $spriteIcon = SpriteIcon::create($this->name);
        $this->setOption("path", $this->name);
        $this->setOption("icons", $spriteIcon->getIcons());
        return parent::render();
    }
}