<?php

namespace Addictic\WordpressFramework\Taxonomies;

use Addictic\WordpressFramework\Fields\Field;
use Addictic\WordpressFramework\Helpers\Container;
use PostTypes\Columns;
use PostTypes\Taxonomy;
use PostTypes\Taxonomy as BaseTaxonomy;

abstract class AbstractTaxonomy
{
    public $name = "category";
    public $posttypes = [];
    public $hierarchical = false;

    public $fields = [];

    public $priority = 0;

    protected BaseTaxonomy $taxonomy;
    protected Columns $columns;


    public function getName()
    {
        return $this->name;
    }

    public function addSubmenu($parentSlug, $position = null)
    {
        $taxonomy = $this->taxonomy;
        add_action("admin_menu", function () use ($taxonomy, $parentSlug, $position) {
            add_submenu_page(
                $parentSlug,
                Container::get("translator")->trans("{$this->name}.page_title"),
                Container::get("translator")->trans("{$this->name}.menu_title"),
                "manage_options",
                "edit-tags.php?taxonomy=" . $this->name,
                null,
                $position
            );
        });
    }

    public function addField(Field $field)
    {
        $this->fields[$field->name] = $field;
        $field->setTaxonomy($this);
        return $this;
    }

    public function register()
    {
        $trans = Container::get("translator");
        $this->taxonomy = new Taxonomy([
            'name' => $this->name,
            'singular' => $trans->trans("{$this->name}.singular"),
            'plural' => $trans->trans("{$this->name}.plural"),
            'slug' => $this->name,
        ]);
        $this->columns = $this->taxonomy->columns();

        if ($this->posttypes) $this->taxonomy->posttype(explode(",", $this->posttypes));
        $this->configure();
        $this->taxonomy->register();
    }

    public function addColumn($name, $opts = [])
    {
        $opts = array_merge([
            'sortable' => false
        ], $opts);

        $translator = Container::get("translator");

        $this->columns->add([
            $name => $translator->has("$this->name.$name.label") ? $translator->trans("$this->name.$name.label") : $translator->trans("$this->name.$name")
        ]);

        if ($opts['sortable']) $this->columns->sortable([
            $name => [$name, true]
        ]);

        if ($opts['callback']) $this->columns->populate($name, $opts['callback']);

        return $this;
    }

    abstract protected function configure();
}