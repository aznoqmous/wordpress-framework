<?php

namespace Addictic\WordpressFramework\PostTypes;

use Addictic\WordpressFramework\Annotation\PostType;
use Addictic\WordpressFramework\Fields\FormationRelationField;
use Addictic\WordpressFramework\Fields\Framework\CheckboxField;
use Addictic\WordpressFramework\Fields\Framework\Field;
use Addictic\WordpressFramework\Fields\Framework\RelationField;
use Addictic\WordpressFramework\Fields\Framework\SelectField;
use Addictic\WordpressFramework\Fields\Framework\TextareaField;
use Addictic\WordpressFramework\Fields\Framework\UploadField;
use Addictic\WordpressFramework\Models\FormationCategoryModel;
use Addictic\WordpressFramework\Models\FormationModel;
use Addictic\WordpressFramework\Models\ThemeTaxonomyModel;

/**
 * @PostType(name="formation", icon="dashicons-book", taxonomies="theme,job")
 */
class FormationPostType extends AbstractPostType
{
    protected function configure()
    {
        $this
            ->addColumn("reference", [
                'callback' => function ($column, $post_id) {
                    $formation = FormationModel::findById($post_id);
                    if (!$formation) return "";
                    $color = $formation->getColor();
                    return $color ? "<span class='color-text' style=\"--color:{$color}\"'>{$formation->getValue("reference")}</span>" : "";
                }
            ])
            ->addColumn("title");

        $this->columns->set($this->columns->add);

        $this->postType->options([
            'show_in_rest' => true,
            'supports' => array('title', 'editor', 'thumbnail', 'taxonomies'),
        ]);

        $this
            ->addMetabox("information_legend")
            ->addField(new SelectField("formationType", [
                'options' => [
                    'default' => 'Formation',
                    'specialisation' => "Spécialisation",
                    'habilitation' => "Habilitation"
                ],
                "class" => "w50 clr"
            ]))
            ->addField(new Field("reference", ['required' => true, "class" => "w50"]))
            ->addField(new Field("level", ['required' => true, "class" => "w50"]))
            ->addField(new TextareaField("description", ['editor' => "wp_editor"]))
            ->addField(new TextareaField("goals", ['editor' => "wp_editor"]))
            ->addField(new TextareaField("required_level", ['editor' => "wp_editor"]))
            ->apply()
            ->addMetabox("session_legend")
            ->addField(new Field("cost", ['required' => false, "class" => "w50"]))
            ->addField(new Field("duration", ['required' => true, "class" => "w50"]))
            ->addField(new Field("participants", ['required' => true, "class" => "w50"]))
            ->addField(new SelectField("location", [
                'options' => [
                    'site' => 'Présentiel',
                    'remote' => "Distanciel"
                ],
                "class" => "w50"
            ]))
            ->addField(new CheckboxField("remoteAvailable", ['default' => true, "class" => "w50"]))
            ->addField(new CheckboxField("cpfAvailable", ['default' => true, "class" => "w50"]))
            ->addField(new UploadField("brochure", ['multiple' => false]))
            ->apply()
            ->addMetaBox("option_legend")
            ->addField(new Field("option_title"))
            ->addField(new TextareaField("option_description", ['editor' => "wp_editor"]))
            ->addField(new Field("option_cost", ['class' => "w50"]))
            ->addField(new Field("option_duration", ['class' => "w50"]))
            ->apply()
        ;

        $this->addSEO();
        $this->addDefaultPattern(get_option("formationDefaultPattern"));
        $this->addDuplicateAction();
    }
}