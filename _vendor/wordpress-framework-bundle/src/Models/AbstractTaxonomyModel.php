<?php

namespace Addictic\WordpressFrameworkBundle\Models;

use Addictic\WordpressFrameworkBundle\Annotation\TaxonomyManager;
use Addictic\WordpressFrameworkBundle\Helpers\PostQueryBuilder;
use Addictic\WordpressFrameworkBundle\Helpers\WpmlHelper;
use Addictic\WordpressFrameworkBundle\Traits\DataTrait;

abstract class AbstractTaxonomyModel extends AbstractModel
{
    protected static $strKey = "term_taxonomy_id";
    public static $strName = "category";
    protected static $strPrefix = "tax";

    public function __construct($datas)
    {
        $this->id = $datas->term_id;
        $this->setData($datas);
    }

    protected static function getBaseQuery(): PostQueryBuilder
    {
        $type = static::$strName;
        $identifier = static::getElementType();

        $lang = wpml_get_current_language();

        if (WpmlHelper::isTranslatedTaxonomy(static::$strName)) {
            $builder = PostQueryBuilder::create()
                ->from(
                    PostQueryBuilder::create()
                        ->from("wp_term_taxonomy", "wtt")
                        ->join("wp_icl_translations", "wit")
                        ->on("wit.element_id = wtt.term_taxonomy_id")
                        ->where("wit.language_code = \"$lang\"", "wit.element_type =\"$identifier\""),
                    "wtt"
                )
                ->join("wp_terms", "wt")
                ->on("wtt.term_id = wt.term_id")
                ->where("wtt.taxonomy = \"$type\"");
        } else {
            $builder = PostQueryBuilder::create()
                ->from("wp_term_taxonomy", "wtt")
                ->join("wp_terms", "wt")
                ->on("wtt.term_id = wt.term_id")
                ->where("wtt.taxonomy = \"$type\"");
        }

        $tax = TaxonomyManager::getInstance()->getTaxonomy(static::$strName);
        $selects = ["*"];
        foreach ($tax->fields as $name => $field) {
            $selects[] = "wtm_$name.meta_value as $name";
            $builder->join("wp_termmeta", "wtm_$name", "LEFT");
            $builder->on("wtm_$name.term_id = wtt.term_id AND wtm_$name.meta_key = '$name'");
        }
        $builder->select(...$selects);

        return $builder;
    }

    public static function findById($id)
    {
        return static::findOneBy(["wt.term_id = $id"]);
    }

    public static function findByIds($ids = [], $opts = []): ?ModelCollection
    {
        if (is_int($ids)) $ids = [$ids];
        if (!is_array($ids) || !count($ids)) return null;

        $results = static::findBy([
            "wt.term_id in ( " . implode(",", $ids) . " )"
        ], $opts);
        $models = [];
        foreach ($results as $result) {
            $index = array_search($result->id, $ids);
            $models[$index] = $result;
        }
        ksort($models);
        return new ModelCollection($models);
    }

    public function getFrontendUrl()
    {
        return "";
    }
}