<?php

namespace Addictic\WordpressFramework\Helpers;


class Metas
{
    private static $instance;
    protected $metas_name = [];
    protected $metas_property = [];

    private function __construct()
    {
        self::$instance = $this;

        $this->add("title", $this->loadDefaultPostValue("meta_title") ?: get_the_title());
        $this->add("description", $this->loadDefaultPostValue("meta_description") ?: get_the_excerpt());

        if ($ogImage = Config::get("ogImage")) {
            $path = ImageHelper::getImagePathById($ogImage);
            if ($path) $this->addOgImage($path);
        }
    }

    public static function getInstance()
    {
        if (!self::$instance) return new self();
        return self::$instance;
    }

    public function add($key, $value)
    {
        $value = strip_tags($value);
        switch ($key) {
            case "description":
                $value = StringHelper::substr($value, 160);
                break;
        }
        $this->metas_name[$key] = $value;
    }

    public function addProperty($key, $value)
    {
        $this->metas_property[$key] = $value;
    }

    public function getMetas()
    {
        return $this->metas_name;
    }

    public function getMetaProperties()
    {
        return $this->metas_property;
    }

    public function loadDefaultPostValue($metaKey)
    {
        $id = get_the_ID();
        if (!$id) return $id;
        $values = get_post_meta($id, $metaKey);
        return $values ? $values[0] : null;
    }

    public function addOgImage($path)
    {
        if(!$path) return;
        $this->addProperty("og:image", $path);
        $this->addProperty("og:image:url", $path);
        $this->addProperty("og:image:secure_url", $path);

        $size = getimagesize($path);
        $this->addProperty("og:image:width", $size[0]);
        $this->addProperty("og:image:height", $size[1]);
    }

    public static function render()
    {
        $arrHtml = [];

        $metas = self::getInstance()->getMetas() ?: [];
        if (isset($metas['title'])) $arrHtml[] = "<title>{$metas['title']}</title>";
        foreach ($metas as $key => $value) $arrHtml[] = "<meta name=\"$key\" content=\"$value\">";

        $metas_property = self::getInstance()->getMetaProperties() ?: [];
        foreach ($metas_property as $key => $value) $arrHtml[] = "<meta property=\"$key\" content=\"$value\">";

        return implode("", $arrHtml);
    }
}