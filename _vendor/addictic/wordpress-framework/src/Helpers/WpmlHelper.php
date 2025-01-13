<?php

namespace Addictic\WordpressFramework\Helpers;

class WpmlHelper
{
    public static function disable()
    {
        add_filter("wpml_is_translated_post_type", function () {
            return false;
        });
        add_filter("wpml_is_translated_taxonomy", function () {
            return false;
        });
    }

    public static function isTranslatedPostType($postType): bool
    {
        return apply_filters("wpml_is_translated_post_type", null, $postType) ?? false;
    }

    public static function isTranslatedTaxonomy($taxonomy): bool
    {
        return apply_filters("wpml_is_translated_taxonomy", null, $taxonomy) ?? false;
    }

    public static function getTranslatedObject($id, $type, $returnOriginalIfMissing = false, $lang = null)
    {
        return apply_filters("wpml_object_id", $id, $type, $returnOriginalIfMissing, $lang);
    }

    public static function isTranslated($postTypeOrTaxonomy)
    {
        return self::isTranslatedPostType($postTypeOrTaxonomy) or self::isTranslatedTaxonomy($postTypeOrTaxonomy);
    }

    public static function getCurrentLanguage()
    {
        return function_exists("wpml_get_current_language") ? wpml_get_current_language() : null;
    }

    public static function getLanguages()
    {
        return function_exists("wpml_get_active_languages") ? wpml_get_active_languages() : null;
    }

    public static function getDefaultLanguage()
    {
        return function_exists("wpml_get_default_language") ? wpml_get_default_language() : null;
    }

    public static function isDefaultLanguage()
    {
        return self::getCurrentLanguage() == self::getDefaultLanguage();
    }

    public static function getCurrentSiteRoot()
    {
        return RoutingHelper::getHost() . "/" . (self::isDefaultLanguage() ? "" : self::getCurrentLanguage());
    }

}