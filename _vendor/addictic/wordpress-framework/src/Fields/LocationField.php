<?php

namespace Addictic\WordpressFramework\Fields;

class LocationField extends Field
{
    protected string $strTemplate = "location";

    public function render()
    {
        $value = $this->getValue();

        if($value){
            $data = json_decode($value);
            $this->setOption("searchLabel", $data->label);
        }

        return parent::render();
    }
}