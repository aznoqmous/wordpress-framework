<?php

namespace Addictic\WordpressFramework\PostTypes;

use Addictic\WordpressFramework\Annotation\PostType;
use Addictic\WordpressFramework\Fields\FormationRelationField;
use Addictic\WordpressFramework\Fields\Framework\Field;
use Addictic\WordpressFramework\Fields\Framework\ListField;
use Addictic\WordpressFramework\Fields\Framework\SelectField;
use Addictic\WordpressFramework\Fields\Framework\TextareaField;
use Addictic\WordpressFramework\Models\CourseModel;
use Addictic\WordpressFramework\Models\FormationModel;

/**
 * @PostType(name="course", icon="dashicons-bank", taxonomies="theme,job")
 */
class CoursePostType extends AbstractPostType
{
    protected function configure()
    {
        $this
            ->addColumn("theme", [
                'callback' => function ($column, $post_id) {
                    $course = CourseModel::findById($post_id);
                    $theme = $course->getTheme();
                    $color = $course->getColor();
                    return $color ? "<span class='color-text' style=\"--color:{$color}\"'>{$theme->name}</span>" : "";
                }
            ])
            ->addColumn("title");
        $this->columns->set($this->columns->add);


        $this
            ->addMetabox("content")
            ->addField(new ListField("formations", [
                'fields' => [
                    'formation' => new FormationRelationField("formation", [
                        'class' => 'w50'
                    ]),
                    'specialisations' => new FormationRelationField("specialisations", [
                        'multiple' => true,
                        'class' => 'w50'
                    ]),
                ]
            ]))
            ->addField(new ListField("arbitraryLink", [
                'fields' => [
                    'formation' => new FormationRelationField("from", [
                        'class' => 'w50'
                    ]),
                    'specialisations' => new FormationRelationField("to", [
                        'class' => 'w50'
                    ]),
                ]
            ]))
            ->apply()
            ->addMetabox("alert")
                ->addField(new SelectField("alertLevel", [
                    'options' => [
                        'info' => "Information",
                        'warning' => "Attention",
                        'danger' => "Danger",
                    ]
                ]))
                ->addField(new TextareaField("alert"))
            ->apply();

        $this->addSEO();

    }
}