<?php

namespace Addictic\WordpressFramework\Models;

use Addictic\WordpressFramework\Helpers\Container;

class ModuleModel extends AbstractPostTypeModel
{
    protected static $strName = "module";

    public static function findByUser()
    {
        $ids = json_decode(\SwpmAuth::get_instance()->get("notes") ?? "", true);
        $modules = ModuleModel::findByIds($ids);
        $modules = $modules ? $modules->getModels() : [];
        usort($modules, fn($a,$b)=> $a->title > $b->title ? 1 : -1);
        return $modules;
    }

    public function getColor(): string
    {
        $ids = array_map(fn($el) => $el->term_id, get_the_terms($this->id, "theme") ?: []);
        $themes = count($ids) ? ThemeTaxonomyModel::findByIds($ids)->getModels() : null;
        return $themes ? $themes[0]->getColor() : "var(--color)";
    }

    public function renderCard()
    {
        $this->loadFields();
        return Container::get("twig")->render("modules/card.twig", array_merge($this->row(), [
            'color' => $this->getColor(),
            'href' => $this->module,
            'description' => get_post_field('post_content', $this->id)
        ]));
    }
}