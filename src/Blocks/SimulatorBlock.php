<?php

namespace App\Blocks;

use Addictic\WordpressFramework\Annotation\Block;
use Addictic\WordpressFramework\Blocks\AbstractBlock;
use Addictic\WordpressFramework\Helpers\Config;

/**
 * @Block(name="app/simulator", template="blocks/simulator.twig")
 */
class SimulatorBlock extends AbstractBlock
{

    public function compile($block_attributes, $content)
    {
        $this->calculator = json_encode([
            'average_kwh_cost' => Config::get("average_kwh_cost"),
            'm_squared_to_kwc' => Config::get("m_squared_to_kwc"),
            'parking_space_area' => Config::get("parking_space_area"),
            'northern_third_kwh_year' => Config::get("northern_third_kwh_year"),
            'center_third_kwh_year' => Config::get("center_third_kwh_year"),
            'southern_third_kwh_year' => Config::get("southern_third_kwh_year"),
            'price_ranges' => Config::get("price_ranges"),
            'european_annual_co2_emission' => Config::get("european_annual_co2_emission"),
            'panel_annual_degradation_rate' => Config::get("panel_annual_degradation_rate"),
            'environmental_reference_period' => Config::get("environmental_reference_period"),
            'module_production_co2_to_kwc' => Config::get("module_production_co2_to_kwc"),
            'radioactive_kg_to_mwh' => Config::get("radioactive_kg_to_mwh"),
            'household_consumption' => Config::get("household_consumption"),
            'vehicle_gasole_consumption' => Config::get("vehicle_gasole_consumption"),
            'gasoline_to_co2' => Config::get("gasoline_to_co2"),
            'vehicle_electric_consumption' => Config::get("vehicle_electric_consumption"),
            'earth_size' => Config::get("earth_size"),
        ]);
    }
}