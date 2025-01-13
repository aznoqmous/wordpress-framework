<?php

namespace Addictic\WordpressFramework\PostTypes;

use Addictic\WordpressFramework\Annotation\PostType;
use Addictic\WordpressFramework\Fields\FormationRelationField;
use Addictic\WordpressFramework\Fields\Framework\DateField;
use Addictic\WordpressFramework\Fields\Framework\Field;
use Addictic\WordpressFramework\Fields\Framework\RelationField;
use Addictic\WordpressFramework\Models\FormationModel;
use Addictic\WordpressFramework\Models\SessionModel;

/**
 * @PostType(name="session", icon="dashicons-groups")
 */
class SessionPostType extends AbstractPostType
{
    protected function configure()
    {
        $this->postType->options['supports'] = [''];

        $this
            ->addColumn("formation", [
                'sortable' => true,
                'callback' => function ($column, $post_id) {
                    $ids = get_post_meta($post_id, "formation")[0];
                    $ids = json_decode($ids);
                    $ids = is_array($ids) ? $ids : [$ids];
                    $formations = FormationModel::findByIds($ids);
                    return implode("<br>", $formations->fetchEach("title"));
                }])
            ->addColumn("location")
            ->addColumn("start")
            ->addColumn("end")
            ->removeColumn("title")
            ->removeColumn("date")
        ;

        $this->addMetabox("content")
            ->addField(new Field("location", [
                'required' => true,
                'class' => "w50"
            ]))
            ->addField(new FormationRelationField("formation", [
                'required' => true,
                'multiple' => true,
                'class' => "w50"
            ]))
            ->addField(new DateField("start", [
                'required' => true,
                'multiple' => false,
                'class' => "w50"
            ]))
            ->addField(new DateField("end", [
                'required' => true,
                'multiple' => false,
                'class' => "w50"
            ]))
            ->addField(new DateField("start_2", [
                'required' => false,
                'multiple' => false,
                'class' => "w50"
            ]))
            ->addField(new DateField("end_2", [
                'required' => false,
                'multiple' => false,
                'class' => "w50"
            ]))
            ->applyToPostType("session");

        $this->addSaveCallback(function($post_id, $post){
            $session = SessionModel::findById($post_id);
            $formations = $session->getFormations();
            if(!$formations) return;

            $title = implode(", ", $formations->fetchEach("title"));

            wp_update_post([
                'ID' => $post_id,
                'post_title' => $title
            ]);
        });
    }
}