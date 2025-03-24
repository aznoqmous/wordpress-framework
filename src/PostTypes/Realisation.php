<?php

namespace App\PostTypes;

use Addictic\WordpressFramework\Annotation\PostType;
use Addictic\WordpressFramework\Fields\DateField;
use Addictic\WordpressFramework\Fields\InputField;
use Addictic\WordpressFramework\Fields\LocationField;
use Addictic\WordpressFramework\Fields\RelationField;
use Addictic\WordpressFramework\Fields\SelectField;
use Addictic\WordpressFramework\Fields\TextareaField;
use Addictic\WordpressFramework\Fields\UploadField;
use Addictic\WordpressFramework\PostTypes\AbstractPostType;
use App\Models\RealisationModel;
use App\Models\TestimonyModel;

/**
 * @PostType(name="realisation", icon="dashicons-location", taxonomies="realisation_option")
 */
class Realisation extends AbstractPostType
{
    protected function configure(){
        $this->postType->options([
            'show_in_rest' => true,
            'supports' => array('title', 'editor', 'thumbnail', 'taxonomies'),
        ]);

        $this
            ->addMetabox("content_legend")
                ->addField(new UploadField("images", ['multiple' => true]))
                ->addField(new UploadField("videos", ['multiple' => true]))
                ->addField(new TextareaField("description"))
                ->addField(new LocationField("address", [
                    'locationType' => "housenumber",
                    'required' => true
                ]))
            ->apply()
            ->addMetaBox("relation_legend")
//                ->addField(new RelationField("product", ['multiple' => false]))
                ->addField(new RelationField("testimonials", [
                    'multiple' => true,
                    'model' => TestimonyModel::class
                ]))
            ->apply()
            ->addMetaBox("stats_legend")
                ->addField(new SelectField("funding_mode", ['options' => [
                    "test_1", "test_2", "test_3"
                ]]))
                ->addField(new SelectField("sector", ["options" => [
                    "test_1", "test_2", "test_3"
                ]]))
                ->addField(new InputField("area", ["required" => true, "input" => ["type" => "number"]]))
                ->addField(new InputField("power", ["required" => true, "input" => ["type" => "number"]]))
                ->addField(new InputField("annual_production", ["required" => true, "input" => ["type" => "number"]]))
                ->addField(new InputField("coverage", ["required" => true, "input" => ["type" => "number"]]))
                ->addField(new DateField("start_date", ["required" => true]))
                ->addField(new InputField("parking_spaces", ["required" => true, "input" => ["type" => "number"]]))
//                ->addField(new RelationField("options"))
            ->apply()
        ;

        $this->addSaveCallback(function($id){
            $realisation = RealisationModel::findById($id);
            if(!$realisation) return;
            if($address = $realisation->getValue("address")){
                $address = json_decode($address);
                update_post_meta($realisation->id, "latitude", $address->lat);
                update_post_meta($realisation->id, "longitude", $address->lng);
            }
        });
    }
}