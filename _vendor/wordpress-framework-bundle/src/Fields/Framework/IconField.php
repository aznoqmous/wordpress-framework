<?php

namespace Addictic\WordpressFrameworkBundle\Fields\Framework;

class IconField extends Field
{
    protected string $strTemplate = "icon";

    public function render()
    {
        $this->args['icons'] = [
            'mower',
            'oil',
            'water',
            'elec',
            'engine',
            'hydrogen',
        ];
        return parent::render();
    }
}