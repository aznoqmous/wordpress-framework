<?php

namespace Addictic\WordpressFramework\Models;

use Addictic\WordpressFramework\Annotation\OldPostTypeManager;
use Addictic\WordpressFramework\Helpers\PostPostQueryBuilder;
use Addictic\WordpressFramework\Helpers\PostQueryBuilder;
use Addictic\WordpressFramework\Helpers\QueryBuilder;
use Addictic\WordpressFramework\Helpers\StringHelper;
use Addictic\WordpressFramework\Helpers\WpmlHelper;

abstract class AbstractPostTypeModel extends AbstractModel
{
    protected static $strName = "post";

    protected $datas;
    public $id;
    public $title;
    public $alias;

    protected $arrData = [];

    public function __construct($datas)
    {
        $this->id = $datas->ID;
        $this->title = html_entity_decode($datas->post_title);
        $this->alias = $datas->post_name;
        $this->arrData = [
            'id' => $this->id,
            'title' => $this->title,
            'alias' => $this->alias
        ];
        $this->setData($datas);
    }

    public function __get($key)
    {
        if ($key == "post_title") return $this->title;
        if ($key == "post_name") return $this->alias;
        if ($key == "ID") return $this->id;

        return parent::__get($key);
    }

    public function getFrontendUrl()
    {
        $prefix = $this->language_code && $this->language_code != WpmlHelper::getDefaultLanguage() ? "/$this->language_code" : "";
        return $prefix . "/" . static::$strName . "/" . $this->alias;
    }

    /**
     * Override to load all fields defined in post type
     */
//    protected static function getBaseQuery(): PostQueryBuilder
//    {
//        $baseQuery = parent::getBaseQuery();
//
//        $postType = PostTypeManager::getInstance()->getPostType(static::$strName);
//        $selects = ["*"];
//        foreach ($postType->instance->fields as $name => $field) {
//            $selects[] = "wtm_$name.meta_value as $name";
//            $baseQuery->join("wp_postmeta", "wtm_$name", "LEFT");
//            $baseQuery->on("wtm_$name.post_id = wp_posts.ID AND wtm_$name.meta_key = '$name'");
//        }
//        $baseQuery->select(...$selects);
//        return $baseQuery;
//    }

    public static function findByTaxonomies($ids = [], $opts = [])
    {
        $query = PostQueryBuilder::create()
            ->from(
                PostQueryBuilder::create()
                    ->from("wp_term_relationships", "wtr")
                    ->join("wp_posts", "wp")
                    ->on("wtr.object_id = wp.ID")
                    ->whereIn("wtr.term_taxonomy_id", $ids)
                    ->groupBy("wp.ID")
                ,
                "wp_posts"
            );
        return static::findBy([], $opts, $query);
    }

    protected static function find($queries = [], $opts = [], $baseQuery = null)
    {
        $baseQuery = $baseQuery ?? static::getBaseQuery();
        $baseQuery
            ->where("post_type = \"" . static::$strName . "\"")
            ->where("post_status != \"auto-draft\"")
            ->where("post_status != \"draft\"")
            ->where("post_status != \"trash\"")
        ;

        /* Bind arbitrary defined meta value for searching / ordering purpose */
        if (isset($opts['metas'])) {
            $metasQuery = PostQueryBuilder::create();
            $selects = ['ID as meta_query_object_id'];
            foreach ($opts['metas'] as $name) {
                $selects[] = "wtm_$name.meta_value as $name";
                $metasQuery->join("wp_postmeta", "wtm_$name", "LEFT");
                $metasQuery->on("wtm_$name.post_id = wp_posts.ID AND wtm_$name.meta_key = '$name'");
            }
            $metasQuery->select(...$selects);
            $baseQuery
                ->join($metasQuery, "metas_query")
                ->on("metas_query.meta_query_object_id = wp_posts.ID");
        }

        /* Bind arbitrary defined taxonomies value for searching / ordering purpose */
        if (isset($opts['taxonomies'])) {
            $taxonomiesQuery = PostQueryBuilder::create()->groupBy("wp_posts.ID");
            $selects = ['ID as tax_query_object_id'];
            foreach ($opts['taxonomies'] as $taxonomy) {
                $selects[] = "GROUP_CONCAT(DISTINCT QUOTE(wtr_$taxonomy.term_taxonomy_id) ORDER BY wtr_$taxonomy.term_taxonomy_id) as $taxonomy";
                $taxonomiesQuery
                    ->join("wp_term_relationships", "wtr_$taxonomy")
                    ->on("wp_posts.ID = wtr_$taxonomy.object_id")
                    ->join("wp_term_taxonomy", "wtt_$taxonomy")
                    ->on("wtr_$taxonomy.term_taxonomy_id = wtt_$taxonomy.term_taxonomy_id")
                    ->join("wp_terms", "wt_$taxonomy")
                    ->on("wtr_$taxonomy.term_taxonomy_id = wt_$taxonomy.term_id")
                    ->where("wtt_$taxonomy.taxonomy = \"$taxonomy\"");
            }
            $taxonomiesQuery->select(...$selects);
            $baseQuery
                ->join($taxonomiesQuery, "tax_query")
                ->on("tax_query.tax_query_object_id = wp_posts.ID");
        }

        $qb = PostQueryBuilder::create()
            ->from($baseQuery, "wp_posts");
        if (count($queries)) $qb->where(...$queries);
        if (isset($opts['order'])) $qb->orderBy($opts['order']);
        if (isset($opts['limit'])) $qb->limit($opts['limit']);
        if (isset($opts['offset'])) $qb->offset($opts['offset']);
        return $qb->query();
    }

    public static function findBySourceLanguageIds($ids)
    {
        return self::findBy([
            "source_language_id IN ( " . implode(",", $ids) . " )"
        ]);
    }

    public static function findActive($opts = []): ?self
    {
        return self::findOneBy(['ID = ' . get_the_ID()], $opts);
    }

    public static function findByPostMeta($key, $value, $opts = [])
    {
        $results = PostQueryBuilder::create()
            ->select("post_id as id")
            ->from("wp_postmeta")
            ->where("meta_key = '$key'")
            ->where("meta_value LIKE '$value'")
            ->query();
        $ids = array_map(fn($result) => $result->id, $results);
        return static::findByIds($ids, $opts);
    }

    public static function searchPost($query)
    {
        return self::findBy(
            ["weight > 0"],
            ['order' => "weight DESC"],
            self::getBaseQuery()
            ->select("*")
            ->addTaxonomy("job", "jobs", "name")
            ->addMeta("reference")
            ->addMeta("meta_title")
            ->addMeta("meta_description")
            ->weightedSearch([
                "post_title" => 1000,
                "meta_title" => 1000,
                "meta_description" => 100,
                "jobs" => 10,
                "post_content" => 1,
            ], "LIKE \"%$query%\"")
        );
    }

    protected function getPostType()
    {
        return OldPostTypeManager::getInstance()->getPostType(static::$strName);
    }

    public function getTerms($taxonomy)
    {
        return new ModelCollection(get_the_terms($this->id, $taxonomy));
    }

    public function parseContent()
    {
        return StringHelper::parseBlock(get_post_field('post_content', $this->id));
    }

    public function loadFields()
    {
        $entity = $this->getPostType()->instance;
        foreach ($entity->fields as $key => $field) {
            $value = $this->getValue($key);
            $this->{$key} = $value;
        }
    }

    public function getValue($key)
    {
        $result = get_post_meta($this->ID, $key);
        return $result ? $result[0] : null;
    }

    public function getPost()
    {
        return get_post($this->id);
    }
}