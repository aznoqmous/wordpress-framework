<?php


namespace Addictic\WordpressFramework\Models\Legacy;

use Addictic\WordpressFramework\Helpers\Container;
use Addictic\WordpressFramework\Helpers\QueryBuilder;
use Addictic\WordpressFramework\Models\AbstractPostTypeModel;

class PostModel extends AbstractPostTypeModel
{
    protected static $strName = "post";

    public function __construct($datas)
    {
        parent::__construct($datas);
        $this->setData($datas);
    }

    public function __get($key)
    {
        switch ($key) {
            case "excerpt":
                return get_the_excerpt($this->id);
            case "image":
                return get_the_post_thumbnail($this->id, 'large');
            case "thumbnail":
                return get_the_post_thumbnail($this->id, 'thumbnail');
            default:
                break;
        }
        return parent::__get($key);
    }

    public function findOtherNews()
    {
        return self::findBy([
            "ID != {$this->id}"
        ], [
            'limit' => 3,
            'order' => "post_date DESC"
        ]);
    }

    public function getFrontendUrl()
    {
        return get_permalink($this->id);
    }

    public function getDate($format)
    {
        return get_the_date($format, $this->id);
    }

    public function getCategories()
    {
        return get_the_category($this->id);
    }

    public function getTags()
    {
        return get_the_tags($this->id);
    }

    public function getExcerpt()
    {
        return $this->excerpt;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function getThumbnail()
    {
        return $this->image;
    }

    public function renderItem()
    {

        return Container::get("twig")->render("posts/item.twig", [
            'post' => $this,
            'categories' => $this->getCategories()
        ]);
    }
}