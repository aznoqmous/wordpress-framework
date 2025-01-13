<?php

namespace Addictic\WordpressFramework\Blocks;

use Addictic\WordpressFramework\Annotation\Block;
use Addictic\WordpressFramework\Helpers\Config;
use Addictic\WordpressFramework\Helpers\Container;
use Addictic\WordpressFramework\Helpers\TwigLoader;
use Addictic\WordpressFramework\Models\JobTaxonomyModel;
use Addictic\WordpressFramework\Models\Legacy\PostModel;
use Addictic\WordpressFramework\Models\ThemeTaxonomyModel;

/**
 * @Block(name="ifc/formation-taxonomy-list", template="blocks/formation-taxonomy-list.twig")
 */
class FormationTaxonomyListBlock extends AbstractBlock
{
    public function compile($block_attributes, $content)
    {
        $this->jobs = JobTaxonomyModel::findJobs()->each(function ($el) {
            $el->href = $el->getFrontendUrl();
        })->fetchRows();

        $subthemes = ThemeTaxonomyModel::findSubthemes();

        $this->subthemes = $subthemes->each(function ($el) {
            $el->image = $el->getImagePath();
            $el->href = $el->getFrontendUrl();
            $parentThemes = ThemeTaxonomyModel::findBySourceLanguageIds(json_decode($el->parents));
            $el->parentThemes = $parentThemes ? $parentThemes->map(fn($el)=> $el->name) : [];
            return $el;
        })->fetchRows();
    }
}