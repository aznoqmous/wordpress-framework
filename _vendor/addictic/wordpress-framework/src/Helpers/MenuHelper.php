<?php

namespace Addictic\WordpressFramework\Helpers;


use Addictic\WordpressFramework\Models\Legacy\PageModel;

class MenuHelper
{
    public static function registerNavMenus(array $locations = [])
    {
        add_action("init", function()use($locations){
            $results = [];
            foreach ($locations as $location) $results[$location] = Container::get("translator")->trans("menus.$location");
            register_nav_menus($results);
        });
    }

    public static function getMenuByLocation(string $location): array
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

}