<?php

namespace Addictic\WordpressFramework\Models;

use Addictic\WordpressFramework\Helpers\Config;
use Addictic\WordpressFramework\Helpers\MenuHelper;
use Addictic\WordpressFramework\Helpers\QueryBuilder;
use Addictic\WordpressFramework\Helpers\WpmlHelper;
use WpOrg\Requests\Exception;

class ThemeTaxonomyModel extends AbstractTaxonomyModel
{
    public static $strName = "theme";

    public function getFrontendUrl(): string
    {
        $formationPage = MenuHelper::getFormationsPage();
        return $formationPage->getFrontendUrl() . "?theme=" . $this->slug;
    }

    public function getIconVar(): string
    {
        return $this->icon ? "var(--icon-{$this->icon})" : "";
    }

    public function getParentTheme(): ?self
    {
        return self::findById($this->parent);
    }

    public function getColor()
    {
        if ($this->color) return $this->color;
        if ($theme = $this->getParentTheme()) return $theme->getColor();
        return "";
    }

    public function getImagePath()
    {
        $images = explode(",",  $this->image);
        $images = array_values(array_filter($images, fn($el)=> $el));
        return count($images) ? wp_get_attachment_url($images[0]) : null;
    }

    public function getQuizzImagePath()
    {
        $images = explode(",",  $this->quizzImage);
        $images = array_values(array_filter($images, fn($el)=> $el));
        return count($images) ? wp_get_attachment_url($images[0]) : null;
    }

    public static function findSubthemes()
    {
        $themeIds = Config::getJson("themes") ?: [];
        if(!WpmlHelper::isDefaultLanguage()) $subthemes = ThemeTaxonomyModel::findBySourceLanguageIds($themeIds);
        else $subthemes = ThemeTaxonomyModel::findByIds($themeIds);
//        dump($themeIds, $subthemes);
        return $subthemes;
    }
}