<?php

namespace Addictic\WordpressFramework\Taxonomies;

use Addictic\WordpressFramework\Fields\Framework\Field;
use Addictic\WordpressFramework\Helpers\Container;
use PostTypes\Taxonomy;
use PostTypes\Taxonomy as BaseTaxonomy;

abstract class AbstractTaxonomy
{
    public $name = "category";
    public $posttypes = [];

    public $fields = [];

    protected BaseTaxonomy $taxonomy;

    public function addSubmenu($parentSlug, $position = null)
    {
        $taxonomy = $this->taxonomy;
        add_action("admin_menu", function () use ($taxonomy, $parentSlug, $position) {
            add_submenu_page(
                $parentSlug,
                $taxonomy->slug,
                $taxonomy->singular,
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
        $this->taxonomy = new Taxonomy($this->name);
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