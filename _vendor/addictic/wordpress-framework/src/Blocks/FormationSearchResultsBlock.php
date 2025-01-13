<?php

namespace Addictic\WordpressFramework\Blocks;

use Addictic\WordpressFramework\Annotation\Block;
use Addictic\WordpressFramework\Helpers\Container;
use Addictic\WordpressFramework\Helpers\TwigLoader;
use Addictic\WordpressFramework\Models\CourseModel;
use Addictic\WordpressFramework\Models\FormationModel;
use Addictic\WordpressFramework\Models\JobTaxonomyModel;
use Addictic\WordpressFramework\Models\Legacy\PostModel;
use Addictic\WordpressFramework\Models\ThemeTaxonomyModel;

/**
 * @Block(name="ifc/formation-search-results", template="blocks/formation-search-results.twig")
 */
class FormationSearchResultsBlock extends AbstractBlock
{
    public function compile($block_attributes, $content)
    {
        $limit = 12;
        $this->attributes['data-limit'] = $limit;

        $taxonomies = [];
        $theme = isset($_GET['theme']) ? ThemeTaxonomyModel::findOneBy(["slug = \"{$_GET['theme']}\""]) : null;
        $job = isset($_GET['job']) ? JobTaxonomyModel::findOneBy(["slug = \"{$_GET['job']}\""]) : null;
        if ($theme) $taxonomies[] = $theme->term_id;
        if ($job) $taxonomies[] = $job->term_id;

        $this->courses = count($taxonomies)
            ? CourseModel::findWithTaxonomies($taxonomies, [
                'order' => "theme ASC",
                'limit' => $limit
            ])
            : CourseModel::findAll([
                'taxonomies' => ['theme'],
                'order' => "theme ASC",
                'limit' => $limit
            ]);

        $this->formations = count($taxonomies)
            ? FormationModel::findWithTaxonomies($taxonomies, [
                'metas' => ["level"],
                'order' => "theme ASC, level ASC",
                'limit' => $limit])
            : FormationModel::findAll([
                'metas' => ["level"],
                'taxonomies' => ['theme'],
                'order' => "theme ASC, level ASC",
                'limit' => $limit
            ]);
   }
}