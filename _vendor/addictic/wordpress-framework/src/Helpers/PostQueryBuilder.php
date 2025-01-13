<?php

namespace Addictic\WordpressFramework\Helpers;

class PostQueryBuilder extends QueryBuilder
{
    public function addMeta($field)
    {
        $this->join(
            QueryBuilder::create()
                ->select(
                    "ID as meta_{$field}_object_id",
                    "wpm_$field.meta_value as $field"
                )
                ->join("wp_postmeta", "wpm_$field", "LEFT")
                ->on("wpm_$field.post_id = wp_posts.ID AND wpm_$field.meta_key = '$field'"),
            "meta_{$field}"
        )
            ->on("meta_{$field}.meta_{$field}_object_id = wp_posts.ID");
        return $this;
    }

    public function addTaxonomy($taxonomy, $alias=null, $taxonomyField="term_id")
    {
        if(!$alias) $alias = $taxonomy;


        $this->join(
            QueryBuilder::create()
                ->select(
                    "ID as taxonomy_{$taxonomy}_object_id",
                    "GROUP_CONCAT(DISTINCT QUOTE(wt_$taxonomy.$taxonomyField) ORDER BY wt_$taxonomy.$taxonomyField) as $alias"
                )
                ->join("wp_term_relationships", "wtr_$taxonomy")
                ->on("wp_posts.ID = wtr_$taxonomy.object_id")
                ->join("wp_term_taxonomy", "wtt_$taxonomy")
                ->on("wtr_$taxonomy.term_taxonomy_id = wtt_$taxonomy.term_taxonomy_id")
                ->join("wp_terms", "wt_$taxonomy")
                ->on("wtr_$taxonomy.term_taxonomy_id = wt_$taxonomy.term_id")
                ->where("wtt_$taxonomy.taxonomy = \"$taxonomy\"")
                ->groupBy("wp_posts.ID"),
            "taxonomy_{$alias}"
        )
            ->on("taxonomy_{$alias}.taxonomy_{$taxonomy}_object_id = wp_posts.ID");

        return $this;
    }

    public function weightedSearch($arrColumnsWeight=[], $query="= 1", $weightField="weight")
    {
            $strCase = [];
            foreach ($arrColumnsWeight as $field => $cost) {
                $strCase[]= "(CASE WHEN $field $query THEN $cost ELSE 0 END)";
            }
            $strCase = implode(" + ", $strCase);
            $this
                ->addSelect("($strCase) as $weightField")
            ;
            return $this;
    }
}