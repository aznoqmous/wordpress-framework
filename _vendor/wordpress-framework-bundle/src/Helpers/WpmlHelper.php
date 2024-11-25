<?php

namespace Addictic\WordpressFrameworkBundle\Helpers;

class WpmlHelper
{
    public static function isTranslatedPostType($postType): bool
    {
        return apply_filters("wpml_is_translated_post_type", null, $postType) ?? false;
    }

    public static function isTranslatedTaxonomy($taxonomy): bool
    {
        return apply_filters("wpml_is_translated_taxonomy", null, $taxonomy) ?? false;
    }
}