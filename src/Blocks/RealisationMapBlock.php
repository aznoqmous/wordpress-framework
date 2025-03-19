<?php

namespace App\Blocks;

use Addictic\WordpressFramework\Annotation\Block;
use Addictic\WordpressFramework\Blocks\AbstractBlock;
use Addictic\WordpressFramework\Helpers\Config;
use Addictic\WordpressFramework\Models\Legacy\AttachmentModel;
use App\Models\RealisationModel;

/**
 * @property $locations
 * @property $realisations
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

        $this->realisations = json_encode(RealisationModel::findAll()->map(fn($r)=> [
            'id' => $r->id,
            'location' => $r->location,
        ]));
    }
}