<?php

namespace App\Blocks;

use Addictic\WordpressFramework\Annotation\Block;
use Addictic\WordpressFramework\Blocks\AbstractBlock;

/**
 * @Block(name="app/simulator", template="blocks/simulator.twig")
 */
class SimulatorBlock extends AbstractBlock
{

    public function compile($block_attributes, $content)
    {
        // TODO: Implement compile() method.
    }
}