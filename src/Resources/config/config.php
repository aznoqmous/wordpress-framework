<?php

use Addictic\WordpressFramework\Fields\Field;
use Addictic\WordpressFramework\Fields\InputField;
use Addictic\WordpressFramework\Helpers\ViteHelper;
use Addictic\WordpressFramework\Settings\OptionsPage;
use Addictic\WordpressFramework\WordpressFrameworkBundle;

if (is_admin()) {
    ViteHelper::add("backend");
} else {
    ViteHelper::add("frontend");
}

WordpressFrameworkBundle::init();

\Addictic\WordpressFramework\Annotation\PostTypeManager::getInstance()
    ->discover("\\App\\PostTypes", __DIR__ . "/../../PostTypes", \Addictic\WordpressFramework\Annotation\PostType::class);

OptionsPage::create("simulator")
    ->addSection("cost")
        ->addField(new InputField("average_kwh_cost", [
            'input' => [
                'type' => "number",
                'step' => 0.01
            ]
        ]))
    ->addSection("area")
        ->addField(new InputFIeld("m_squared_to_kwc", [
            'input' => [
                'type' => "number",
                'step' => 0.01
            ]
        ]))
        ->addField(new InputField("parking_space_area", [
            'input' => [
                'type' => "number"
            ]
        ]))
    ->addSection("location_factor")
        ->addField(new Field("northern_third_kwh_year"))
        ->addField(new Field("center_third_kwh_year"))
        ->addField(new Field("southern_third_kwh_year"))
    ->addSection("project_cost_factor")
        ->addField(new \Addictic\WordpressFramework\Fields\ListField("price_ranges", [
            'fields' => [
                'lower' => new Field("lower"),
                'higher' => new Field("higher"),
                'value' => new Field("value"),
            ]
        ]))
    ->addSection("environment")
        ->addField(new Field("european_annual_co2_emission"))
        ->addField(new Field("panel_annual_degradation_rate"))
        ->addField(new Field("environmental_reference_period"))
        ->addField(new Field("module_production_co2_to_kwc"))
        ->addField(new Field("radioactive_kg_to_mwh"))
        ->addField(new Field("household_consumption"))
        ->addField(new Field("vehicle_gasole_consumption"))
        ->addField(new Field("gasoline_to_co2"))
        ->addField(new Field("vehicle_electric_consumption"))
        ->addField(new Field("earth_size"));