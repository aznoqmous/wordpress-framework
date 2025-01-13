<?php

namespace Addictic\WordpressFramework\Fields;

/**
 ** Arguments **
 * editor: wp
 */
class TextareaField extends Field
{
    protected string $strTemplate = "textarea";

    public function render()
    {
        $value = $this->getValue();

        $editor = $this->args['editor'] ?? null;
        switch($editor){
            case "wp_editor":
                ob_start();
                wp_editor($value, $this->name);
                $this->args['html'] = ob_get_clean();
                break;
            default:
//                $this->args['field'] = "<textarea class='field'>$value</textarea>";
                break;
        }

        return parent::render();
    }

    public function validate()
    {
        $this->value = stripslashes($this->getValue());
        return parent::validate();
    }

}