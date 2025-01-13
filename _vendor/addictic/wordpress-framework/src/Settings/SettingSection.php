<?php

namespace Addictic\WordpressFramework\Settings;

use Addictic\WordpressFramework\Fields\Field;
use Addictic\WordpressFramework\Helpers\Container;

class SettingSection
{

    public string $name;
    public string $page;
    protected $fields = [];

    public function __construct($name, $optionsPage)
    {
        $this->name = $name;
        $this->page = $optionsPage;

        $this->register();
    }

    private function register()
    {
        add_action("admin_init", function () {
            add_settings_section(
                $this->name,
                Container::get("translator")->trans("{$this->page}.{$this->name}.legend"),
                null,
                $this->page
            );
        });
    }

    public function addField(Field $field)
    {
        $this->fields[] = $field;
        $field->setSettingSection($this);

        $section = $this;
        add_action("admin_init", function () use ($section, $field) {
            register_setting($section->page, $field->name);
            add_settings_field(
                $field->name,
                Container::get("translator")->trans("{$this->page}.{$this->name}.{$field->name}"),
                function () use ($field) {
                    echo $field->render();
                },
                $this->page,
                $this->name
            );
        });

        return $this;
    }

    public function setOptionsPage(string $optionsPage)
    {
        $this->page = $optionsPage;
    }
}