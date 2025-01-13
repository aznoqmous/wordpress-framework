<?php

namespace Addictic\WordpressFramework\Fields;

use Addictic\WordpressFramework\Fields\Framework\RelationField;
use Addictic\WordpressFramework\Models\FormationModel;

class FormationRelationField extends RelationField
{
    public function __construct($name, $args = [])
    {
        parent::__construct($name, $args);
        $this->args['model'] = FormationModel::class;
        $this->args['callback'] = function (FormationModel $formation) {
            $color = $formation->getColor();
            return "<div " . ($color ? "style='--color: {$color}'" : "") . "><strong>{$formation->getValue("reference")}</strong><span>$formation->title</span></div>";
        };
    }

    public function render()
    {
        $this->args['class'] = $this->args['class'] ?? "";
        $this->args['class'] .= " formation-relation";

        return parent::render();
    }
}