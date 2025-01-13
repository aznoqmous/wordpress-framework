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
 * @PostType(name="sales_contact", taxonomies="theme", icon="dashicons-phone", priority=1)
 */
class SalesContactPostType extends AbstractPostType
{
    protected function configure()
    {
        $this->postType->options['supports'] = [''];

        $this->addMetabox("content")
            ->addField(new Field("first_name",['required' => true]))
            ->addField(new Field("last_name",['required' => true]))
            ->addField(new Field("phone",['required' => true]))
        ->apply();

        $this->addSaveCallback(function($post_id){
            wp_update_post([
                'ID' => $post_id,
                'post_title' => get_post_meta($post_id, "first_name")[0] . " " . get_post_meta($post_id, "last_name")[0]
            ]);
        });
    }
}