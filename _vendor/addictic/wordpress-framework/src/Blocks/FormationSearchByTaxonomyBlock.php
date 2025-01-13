<?php

namespace Addictic\WordpressFramework\Blocks;

use Addictic\WordpressFramework\Annotation\Block;
use Addictic\WordpressFramework\Helpers\Container;
use Addictic\WordpressFramework\Helpers\TwigLoader;
use Addictic\WordpressFramework\Models\JobTaxonomyModel;
use Addictic\WordpressFramework\Models\Legacy\PostModel;
use Addictic\WordpressFramework\Models\ThemeTaxonomyModel;

/**
 * @Block(name="ifc/formation-search-by-taxonomy", template="blocks/formation-search-by-taxonomy.twig")
 */
class FormationSearchByTaxonomyBlock extends AbstractBlock
{
    public function compile($block_attributes, $content)
    {
        $this->jobs = JobTaxonomyModel::findJobs()->fetchRows();
        $this->themes = ThemeTaxonomyModel::findSubthemes()->fetchRows();
        $this->headline = $block_attributes['headline'] ?? "";
        $this->description = $block_attributes['description'] ?? "";

        $this->activeTheme =  isset($_GET['theme']) ? ThemeTaxonomyModel::findOneBy(["slug = \"{$_GET['theme']}\""]) : null;
        $this->activeJob = isset($_GET['job']) ? JobTaxonomyModel::findOneBy(["slug = \"{$_GET['job']}\""]) : null;

        if($this->activeTheme) $this->activeTheme = $this->activeTheme->row();
        if($this->activeJob) $this->activeJob = $this->activeJob->row();
    }
}