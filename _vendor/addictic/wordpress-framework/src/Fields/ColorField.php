<?php

namespace Addictic\WordpressFramework\Fields;

class ColorField extends Field
{
    protected string $strTemplate = "color";

    public function render()
    {
        if (!isset($this->args['colors'])) {
            $this->args['colors'] = [
                "white",
                "grey",
                "black"
            ];
        }

        $colors = [];
        $i = 0;
        foreach ($this->args['colors'] as $color) {
            $colors[$i] = $color;
            $i++;
        }
        $this->args['colors'] = $colors;

        return parent::render();
    }
}
