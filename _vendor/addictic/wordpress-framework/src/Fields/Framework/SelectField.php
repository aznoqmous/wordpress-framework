<?php

namespace Addictic\WordpressFramework\Fields\Framework;


class SelectField extends Field
{
    protected string $strTemplate = "select";

    public function render()
    {
        $options = [];

        foreach((array) $this->args['options'] as $key => $option) {
            if(is_integer($key) && is_string($option)) $options[] = (object)[
                'label' => $option,
                'value' => $option
            ];
            else {
                $options[$key] = (object)[
                    'label' => $option,
                    'value' => $key
                ];
            }
        }

        $this->args['options'] = $options;

        return parent::render();
    }

}