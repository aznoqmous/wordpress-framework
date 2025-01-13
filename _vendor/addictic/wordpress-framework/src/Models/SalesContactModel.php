<?php

namespace Addictic\WordpressFramework\Models;

use Addictic\WordpressFramework\Helpers\DateHelper;

class SalesContactModel extends AbstractPostTypeModel
{
    protected static $strName = "sales_contact";

    public static function findActives($opts=[]): ?ModelCollection
    {
        $activePost = get_post();

        if (!$activePost) return null;

        switch ($activePost->post_type) {
            case "course":
                $course = CourseModel::findActive();
                $themes = $course->getTerms("theme");
                $res = self::findByTaxonomies($themes->fetchEach("term_id"));
                return $res ?: null;
            case "formation":
                $formation = FormationModel::findActive();
                $themes = $formation->getTerms("theme");
                $res = self::findByTaxonomies($themes->fetchEach("term_id"));
                return $res ?: null;
            default:
                break;
        }
        return null;
    }
}