<?php

namespace Addictic\WordpressFramework\Models;

use Addictic\WordpressFramework\Helpers\Container;
use Addictic\WordpressFramework\Helpers\PostQueryBuilder;
use Addictic\WordpressFramework\Helpers\WpmlHelper;

abstract class AbstractModel
{
    protected static $strKey = "ID";
    protected static $strTable = "wp_posts";
    protected static $strName = "page";
    protected static $strPrefix = "post";

    protected static $baseQuery = null;

    protected $arrData = [];
    public $id;

    public function __construct($datas)
    {
        $this->setData($datas);
    }

    public function __get($key)
    {
        if (isset($this->arrData[$key])) return $this->arrData[$key];
    }

    public function __set($key, $value)
    {
        if ($key === static::$strKey) {
            $key = "id";
            $this->id = $value;
        }

        $this->arrData[$key] = $value;
    }

    public static function findOneBy($queries, $opts = []): ?self
    {
        $results = static::find($queries, $opts);
        return count($results) ? new static($results[0]) : null;
    }

    public static function findBy($queries = [], $opts = [], $baseQuery = null)
    {
        return static::loadModels(static::find($queries, $opts, $baseQuery));
    }

    public static function findById($id)
    {
        if (!$id) return null;
        $key = static::$strKey;
        return static::findOneBy(["{$key} = $id"]);
    }

    public static function findBySourceLanguageId($id)
    {
        if (!$id) return null;
        return static::findOneBy(["source_language_id = $id"]);
    }

    public static function findByIds($ids = [], $opts = [])
    {
        if (is_int($ids)) $ids = [$ids];
        if (!is_array($ids) || !count($ids)) return null;
        $key = static::$strKey;
        $results = static::findBy([
            "{$key} in (" . implode(",", $ids) . ")"
        ], $opts);

        if (!isset($opts['order'])) {
            // preserve input id order if no order opts set
            $models = [];
            foreach ($results as $result) {
                $index = array_search($result->id, $ids);
                $models[$index] = $result;
            }
            ksort($models);
        } else return $results;

        return new ModelCollection($models);
    }

    public static function findByExcludeIds($queries = [], $ids = [], $opts = [])
    {
        $key = static::$strKey;
        if (is_int($ids)) $ids = [$ids];
        if (is_array($ids) && count($ids)) $queries[] = "{$key} not in (" . implode(",", $ids) . ")";
        return static::findBy($queries, $opts);
    }

    public static function findAll($opts = [], $baseQuery = null)
    {
        return static::findBy([], $opts, $baseQuery);
    }

    protected static function getElementType()
    {
        return static::$strPrefix . "_" . static::$strName;
    }

    protected static function getBaseQuery(): PostQueryBuilder
    {
        return PostQueryBuilder::create();
    }

    protected static function find($queries = [], $opts = [], $baseQuery = null)
    {
        $qb = $baseQuery ?? static::getBaseQuery();
        if (count($queries)) $qb->where(...$queries);
        if (isset($opts['order'])) $qb->orderBy($opts['order']);
        if (isset($opts['limit'])) $qb->limit($opts['limit']);
        if (isset($opts['offset'])) $qb->offset($opts['offset']);
        return $qb->query();
    }

    public static function count($queries = [], $opts = [], $baseQuery = null)
    {
        $qb = $baseQuery ?? static::getBaseQuery();
        if (count($queries)) $qb->where(...$queries);
        if (isset($opts['order'])) $qb->orderBy($opts['order']);
        if (isset($opts['limit'])) $qb->limit($opts['limit']);
        if (isset($opts['offset'])) $qb->offset($opts['offset']);
        return $qb->count();
    }

    public static function loadModels($datas = []): ModelCollection
    {
        return new ModelCollection(array_map(function ($data) {
            return new static($data);
        }, $datas));
    }

    public function row()
    {
        return $this->arrData;
    }

    public function get($key)
    {
        return isset($this->arrData[$key]) ? $this->arrData[$key] : null;
    }

    public function set($key, $value)
    {
        $this->arrData[$key] = $value;
    }

    public function has($key)
    {
        return isset($this->arrData[$key]);
    }

    public function setData($data)
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public static function getName()
    {
        return static::$strName;
    }

    public static function register()
    {
        $GLOBALS['MODELS'][static::$strName] = static::class;
    }

    public static function getClassByName($name)
    {
        return $GLOBALS['MODELS'][$name];
    }

    public static function loadPostModel(\WP_Post $post): ?AbstractModel
    {
        $class = self::getClassByName($post->object);
        $object_id = apply_filters("wpml_object_id", $post->object_id, $class::$strName);
        return $object_id ? $class::findById($object_id) : null;
    }

    public function getFrontendUrl()
    {
        return "/" . $this->url;
    }

    public function save()
    {
        global $wpdb;
        $wpdb->update(
            static::$strTable,
            $this->arrData,
            [
                static::$strKey => $this->id
            ]
        );
    }
}