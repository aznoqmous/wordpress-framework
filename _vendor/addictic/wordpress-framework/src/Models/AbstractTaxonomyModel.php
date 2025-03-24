<?php

namespace Addictic\WordpressFramework\Models;

use Addictic\WordpressFramework\Annotation\TaxonomyManager;
use Addictic\WordpressFramework\Helpers\PostQueryBuilder;
use Addictic\WordpressFramework\Helpers\WpmlHelper;
use Addictic\WordpressFramework\Traits\DataTrait;

abstract class AbstractTaxonomyModel extends AbstractModel
{
    protected static $strKey = "term_taxonomy_id";
    protected static $strTable = "wp_term_taxonomy";

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
        if (WpmlHelper::isTranslatedTaxonomy(static::$strName)) {
            $builder = PostQueryBuilder::create()
                ->select("*", "wt.name as post_title")
                ->from(
                    self::getWpmlQuery(),
                    "wtt"
                )
                ->join("wp_terms", "wt")
                ->on("wtt.term_id = wt.term_id")
                ->where("wtt.taxonomy = \"$type\"")
            ;
        } else {
            $builder = PostQueryBuilder::create()
                ->select("*", "wt.name as post_title")
                ->from("wp_term_taxonomy", "wtt")
                ->join("wp_terms", "wt")
                ->on("wtt.term_id = wt.term_id")
                ->where("wtt.taxonomy = \"$type\"");
        }

        $tax = TaxonomyManager::getInstance()->get(static::$strName);
        $selects = ["*"];
        $fields = property_exists($tax, "fields") ?: [];
        foreach ($fields as $name => $field) {
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

    public static function findBySourceLanguageIds($ids = [], $opts = []): ?ModelCollection
    {
        if (is_int($ids)) $ids = [$ids];
        if (!is_array($ids) || !count($ids)) return null;

        $results = static::findBy([
            "source_language_id in ( " . implode(",", $ids) . " )"
        ], $opts);
        $models = [];
        foreach ($results as $result) {
            $index = array_search($result->source_language_id, $ids);
            $models[$index] = $result;
        }
        ksort($models);
        return new ModelCollection($models);
    }


    public function getFrontendUrl()
    {
        return "";
    }

    public function getParent(): ?self
    {
        return self::findById($this->parent);
    }
}