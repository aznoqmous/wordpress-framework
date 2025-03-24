<?php

namespace App\Blocks;

use Addictic\WordpressFramework\Annotation\Block;
use Addictic\WordpressFramework\Blocks\AbstractBlock;
use Addictic\WordpressFramework\Helpers\Container;
use Addictic\WordpressFramework\Helpers\QueryBuilder;
use App\Models\RealisationModel;
use App\Models\RealisationOptionTaxonomyModel;

/**
 * @property $filters
 * @property $realisations
 * @property $count
 * @Block(name="app/filtered-realisations", template="blocks/filtered-realisations.twig")
 */
class FilteredRealisationsBlock extends AbstractBlock
{
    public function compile($block_attributes, $content)
    {
        $taxonomies = [];

        $taxonomyModels = RealisationOptionTaxonomyModel::findBy(["count != 0"]);

        foreach ($taxonomyModels as $taxonomy) {

            if ($taxonomy->parent) {
                $taxonomies[$taxonomy->parent] = isset($taxonomies[$taxonomy->parent])
                    ? $taxonomies[$taxonomy->parent]
                    : $taxonomy->getParent()->row()
                ;
                if(!isset($parent["children"])) $taxonomies[$taxonomy->parent]["children"] = [];
                $taxonomies[$taxonomy->parent]["children"][] = $taxonomy->row();
            }
            else {
                if(!isset($taxonomies[$taxonomy->term_id]))
                    $taxonomies[$taxonomy->term_id] = $taxonomy->row();
            }


        }

        $this->filters = $taxonomies;

        $this->count = QueryBuilder::create()
            ->from("wp_posts", "wp")
            ->where("wp.post_type = \"realisation\"")
            ->count();

        $this->realisations = Container::get("twig")->render("parts/slider.twig", [
            'items' => RealisationModel::findAll()->map(fn($realisation) => $realisation->renderItem())
        ]);
    }
}