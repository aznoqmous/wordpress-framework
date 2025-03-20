<?php

namespace App\Blocks;

use Addictic\WordpressFramework\Annotation\Block;
use Addictic\WordpressFramework\Blocks\AbstractBlock;
use Addictic\WordpressFramework\Helpers\Config;
use Addictic\WordpressFramework\Helpers\Container;
use Addictic\WordpressFramework\Models\Legacy\AttachmentModel;
use App\Models\RealisationModel;

/**
 * @property $locations
 * @property $realisations
 * @property $realisationPins
 * @Block(name="app/realisation-map", template="blocks/realisation-map.twig")
 */
class RealisationMapBlock extends AbstractBlock
{
    public function compile($block_attributes, $content)
    {
        $file = Config::get("realisationCsv");

        if($file && $file = AttachmentModel::findById($file)){
            $data = [];
            $handle = fopen($file->guid, "r");
            while (($row = @fgetcsv($handle)) !== FALSE) {
                $data[] = $row;
            }
            fclose($handle);
            $this->locations = json_encode($data);
        }

        $this->realisationPins = json_encode(RealisationModel::findAll()->map(fn($r)=> [
            'id' => $r->id,
            'longitude' => $r->getValue("longitude"),
            'latitude' => $r->getValue("latitude")
        ]));

        $this->realisations = Container::get("twig")->render("parts/slider.twig", [
            'items' => RealisationModel::findAll()->map(fn($realisation) => $realisation->renderItem())
        ]);
    }
}