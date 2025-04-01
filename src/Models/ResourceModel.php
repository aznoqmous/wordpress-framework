<?php

namespace App\Models;

use Addictic\WordpressFramework\Helpers\Container;
use Addictic\WordpressFramework\Models\AbstractPostTypeModel;

class ResourceModel extends AbstractPostTypeModel
{
    public static $strName = "resource";

    public function getParent()
    {
        return self::findById($this->post_parent);
    }

    public function getChildren()
    {
        return self::findBy([
            "post_parent = \"{$this->id}\""
        ]);
    }

    public function renderItem()
    {
        $tags = $this->getTags();
        return Container::get("twig")->render("parts/resource-item.twig", array_merge($this->row(), [
            'resource' => $this,
            'excerpt' => $this->getExcerpt(),
            'image' => $this->getFeaturedImage(),
            'tags' => $tags ? $tags->getRows() : null
        ]));
    }

    public function getTags()
    {
        $jobs = get_the_terms(intval($this->id), "resource_tag");
        $ids = array_map(fn($el) => $el->term_id, is_array($jobs) ? $jobs : []);
        return count($ids) ? ResourceTagModel::findByIds($ids) : null;
    }
}