<?php

namespace Addictic\WordpressFramework\PostTypes;

use Addictic\WordpressFramework\Annotation\PostType;
use Addictic\WordpressFramework\Fields\FormationRelationField;
use Addictic\WordpressFramework\Fields\Framework\CheckboxField;
use Addictic\WordpressFramework\Fields\Framework\Field;
use Addictic\WordpressFramework\Fields\Framework\RelationField;
use Addictic\WordpressFramework\Fields\Framework\SelectField;
use Addictic\WordpressFramework\Fields\Framework\TextareaField;
use Addictic\WordpressFramework\Fields\ModuleSelectionField;
use Addictic\WordpressFramework\Models\FormationCategoryModel;
use Addictic\WordpressFramework\Models\FormationModel;
use Addictic\WordpressFramework\Models\ThemeTaxonomyModel;

/**
 * @PostType(name="module", icon="dashicons-media-archive", taxonomies="theme", priority=1)
 */
class ModulePostType extends AbstractPostType
{
    protected function configure()
    {
        $this
            ->addMetabox("content")
//            ->addField(new FormationRelationField("formations", ['multiple' => true]))
            ->addField(new ModuleSelectionField("module"))
            ->apply()
        ;
    }
}