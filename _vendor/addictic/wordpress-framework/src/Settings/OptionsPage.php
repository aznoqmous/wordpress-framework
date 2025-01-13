<?php

namespace Addictic\WordpressFramework\Settings;

use Addictic\WordpressFramework\Helpers\Container;

class OptionsPage
{
    private $name;
    private $sections;

    public $fields = [];

    protected static $instances = [];

    public function __construct($name, $register = true)
    {
        $this->name = $name;
        if ($register) $this->register();
    }

    public static function create($name, $register = true)
    {
        self::$instances[$name] = new static($name, $register);
        return self::$instances[$name];
    }

    public static function get($key)
    {
        return self::$instances[$key];
    }

    private function register()
    {
        add_action("admin_menu", function () {
            add_options_page(
                Container::get("translator")->trans("{$this->name}.title"),
                Container::get("translator")->trans("{$this->name}.title"),
                'manage_options',
                $this->name,
                [$this, 'render']
            );
        });
    }

    function render()
    {
        ob_start();
        settings_fields($this->name);
        do_settings_sections($this->name);
        submit_button();
        $content = ob_get_clean();
        return Container::get("twig")->renderTemplate("backend/options_page.twig", [
            'url' => "yo",
            'content' => $content
        ]);
    }

    public function addSection(string $sectionName)
    {
        $this->sections[] = new SettingSection($sectionName, $this->name);
        return $this;
    }

    public function addField($field)
    {
        $this->sections[count($this->sections) - 1]->addField($field);
        $this->fields[$field->name] = $field;
        return $this;
    }
}