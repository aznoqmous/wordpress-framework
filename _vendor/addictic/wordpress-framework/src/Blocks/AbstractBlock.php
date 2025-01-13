<?php

namespace Addictic\WordpressFramework\Blocks;

use Addictic\WordpressFramework\Helpers\Container;
use Addictic\WordpressFramework\Traits\DataTrait;

abstract class AbstractBlock
{
    use DataTrait;

    protected $strName = "";
    protected $strClass = "";
    protected $strTemplate = "";
    protected $attributes = [];

    public function __construct($name, $template)
    {
        $this->strName = $name;
        $this->strTemplate = $template;
        $this->strClass = str_replace("/", "-", $this->strName);

        add_action('init', function () {
            register_block_type($this->strName, [
                'apiVersion' => 3,
                'render_callback' => function ($block_attributes, $content) {
                    return $this->render($block_attributes, $content);
                },
            ]);
        });

    }

    public function render($block_attributes, $content): string
    {
        $this->compile($block_attributes, $content);
        $this->className = $this->strClass;

        $attributes = [];
        foreach($this->attributes as $key => $value) $attributes[] = "$key=$value";
        $this->attributes = $attributes;
        return Container::get("twig")->render($this->strTemplate, array_merge([
            'attributes' => $this->attributes
        ],  $this->row()));
    }

    abstract public function compile($block_attributes, $content);
}