<?php

namespace Addictic\WordpressFramework\Blocks;

use Addictic\WordpressFramework\Annotation\Block;
use Addictic\WordpressFramework\Helpers\Container;
use Addictic\WordpressFramework\Helpers\TwigLoader;
use Addictic\WordpressFramework\Models\JobOfferModel;
use Addictic\WordpressFramework\Models\Legacy\PostModel;

/**
 * @Block(name="ifc/job-offer-list", template="blocks/job-offer-list.twig")
 */
class JobOfferListBlock extends AbstractBlock
{
    public function compile($block_attributes, $content)
    {
        $this->jobOffers = JobOfferModel::findAll();
    }
}