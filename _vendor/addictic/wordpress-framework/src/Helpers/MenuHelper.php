<?php

namespace Addictic\WordpressFramework\Helpers;


use Addictic\WordpressFramework\Models\Legacy\PageModel;

class MenuHelper
{
    public static function getMenuByLocation($location): array
    {
        $locations = get_nav_menu_locations();
        $menu_obj = get_term($locations[$location], 'nav_menu');
        if ($menu_obj) {
            $navItems = wp_get_nav_menu_items($menu_obj->term_id);
            $items = [];
            foreach ($navItems as $item) {
                if ($item->menu_item_parent) {
                    if (!isset($items[$item->menu_item_parent])) continue;
                    if (!$items[$item->menu_item_parent]->children) $items[$item->menu_item_parent]->children = [];
                    $items[$item->menu_item_parent]->children[] = $item;
                } else $items[$item->ID] = $item;
            }
            return array_values($items);
        }

        return [];
    }

    public static function getFormationsPage()
    {
        $formationPageId = apply_filters("wpml_object_id", get_option('formationsPage'), "page");
        return PageModel::findById($formationPageId);
    }

    public static function isFormationPage($id)
    {
        return apply_filters("wpml_object_id", $id, "page", true, "fr") == get_option("formationsPage");
    }
}