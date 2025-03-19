<?php

namespace App\PostTypes;

use Addictic\WordpressFramework\Annotation\PostType;
use Addictic\WordpressFramework\Fields\DateField;
use Addictic\WordpressFramework\Fields\InputField;
use Addictic\WordpressFramework\Fields\RelationField;
use Addictic\WordpressFramework\Fields\SelectField;
use Addictic\WordpressFramework\Fields\TextareaField;
use Addictic\WordpressFramework\Fields\UploadField;
use Addictic\WordpressFramework\PostTypes\AbstractPostType;
use App\Models\TestimonyModel;

/**
 * @PostType(name="realisation")
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
                ->addField(new TextareaField("address"))
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
                ->addField(new InputField("area", ["required" => true]))
                ->addField(new InputField("power", ["required" => true]))
                ->addField(new InputField("annual_production", ["required" => true]))
                ->addField(new InputField("coverage", ["required" => true]))
                ->addField(new DateField("start_date", ["required" => true]))
                ->addField(new InputField("parking_spaces", ["required" => true]))
                ->addField(new SelectField("sector", ["options" => [
                    "test_1", "test_2", "test_3"
                ]]))
//                ->addField(new RelationField("options"))
            ->apply()
        ;
    }
}