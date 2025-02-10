<?php

namespace Addictic\WordpressFramework\Fields;

class InputField extends Field
{
    protected string $strTemplate = "input";

    public function render()
    {
        $this->args['input'] = array_merge(['type' => "text"], isset($this->args['input']) ? $this->args['input'] : []);
        $this->args['input'] = $this->arrayToAttributes($this->args['input']);
        return parent::render();
    }
}