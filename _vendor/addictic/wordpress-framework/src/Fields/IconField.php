<?php

namespace Addictic\WordpressFramework\Fields;

use Addictic\WordpressFramework\Helpers\SpriteIcon;

class IconField extends Field
{
    protected string $strTemplate = "icon";

    public function render()
    {
        $this->args['name'] = isset($this->args['name']) ?: "icons";
        $spriteIcon = SpriteIcon::create($this->args['name']);
        $this->setOption("path", $this->args['name']);
        $this->setOption("icons", $spriteIcon->getIcons());
        return parent::render();
    }
}
