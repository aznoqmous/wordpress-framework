<?php

use Addictic\WordpressFramework\Fields\Field;
use Addictic\WordpressFramework\Fields\InputField;
use Addictic\WordpressFramework\Helpers\AssetsHelper;
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

\Addictic\WordpressFramework\Annotation\BlockManager::getInstance()
    ->discover("\\App\\Blocks", __DIR__ . "/../../Blocks", \Addictic\WordpressFramework\Annotation\Block::class);


\App\Models\TestimonyModel::register();

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
            'lower' => new Field("kwc_min", ["class" => "w50"]),
            'value' => new InputField("cost", [
                "class" => "w50",
                'input' => [
                    'type' => "number",
                    'step' => 0.01
                ]
            ]),
        ]
    ]))
    ->addSection("environment")
    ->addField(new InputField("european_annual_co2_emission", [
        'input' => ['type' => "number"]
    ]))
    ->addField(new InputField("panel_annual_degradation_rate", [
        'input' => [
            'type' => "number",
            'step' => 0.01
        ]
    ]))
    ->addField(new InputField("environmental_reference_period", [
        'input' => ['type' => "number"]
    ]))
    ->addField(new InputField("module_production_co2_to_kwc", [
        'input' => ['type' => "number"]
    ]))
    ->addField(new InputField("radioactive_kg_to_mwh", [
        'input' => ['type' => "number", 'step' => 0.0001]
    ]))
    ->addField(new InputField("household_consumption", [
        'input' => ['type' => "number"]
    ]))
    ->addField(new InputField("vehicle_gasole_consumption", [
        'input' => ['type' => "number"]
    ]))
    ->addField(new InputField("gasoline_to_co2", [
        'input' => ['type' => "number", 'step' => 0.01]
    ]))
    ->addField(new InputField("vehicle_electric_consumption", [
        'input' => ['type' => "number"]
    ]))
    ->addField(new InputField("earth_size", [
        'input' => ['type' => "number"]
    ]));