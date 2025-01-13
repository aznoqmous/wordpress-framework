<?php

namespace Addictic\WordpressFramework\Taxonomies;

use Addictic\WordpressFramework\Annotation\Taxonomy;
use Addictic\WordpressFramework\Fields\Framework\ColorField;
use Addictic\WordpressFramework\Fields\Framework\IconField;
use Addictic\WordpressFramework\Fields\Framework\RelationField;
use Addictic\WordpressFramework\Fields\Framework\UploadField;
use Addictic\WordpressFramework\Models\ThemeTaxonomyModel;

/**
 * @Taxonomy(name="theme")
 */
class ThemeTaxonomy extends AbstractTaxonomy
{
    protected function configure()
    {
        $this->taxonomy->options(["show_in_rest" => true]);
        $this
            ->addField(new UploadField("image"))
            ->addField(new UploadField("quizzImage"))
            ->addField(new IconField("icon"))
            ->addField(new ColorField("color", [
                'colors' => [
                    'red' => "#8C1812",
                    'orange' => "#ED6E19",
                    'purple' => "#5F2B86",
                    'blue' => "#2C7CC0",
                    'grey' => "#6B6767",
                    'green' => "#73AF46",
                ]
            ]))
            ->addField(new RelationField("parents", [
                'model' => ThemeTaxonomyModel::class,
                'multiple' => true,
                'sortable' => true,
                'callback' => fn($item)=> $item->name
            ]))
        ;

        $this->addColumn("color", [
            'callback' => function ($what, $column, $post_id) {
                $taxonomy = ThemeTaxonomyModel::findById($post_id);
                $color = $taxonomy->getColor();
                return $color ? "<span class='color-badge' style=\"--color:{$color}\"'></span>" : "";
            }
        ]);
    }
}