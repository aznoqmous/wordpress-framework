<?php

namespace Addictic\WordpressFramework\Annotation;

/**
 * @Annotation
 */
class Route
{
    public $value;
    public $namespace = "api";
    public $name = "route";
    public $methods = "GET";
    public $priority = 0;

    public function getRoute()
    {
        return "/wp-json{$this->namespace}{$this->value}";
    }

    public function route($route, $callback, $methods = "GET")
    {
        add_action("rest_api_init",
            function () use ($route, $callback, $methods) {
                register_rest_route(
                    $this->namespace,
                    $route, [
                    "methods" => $methods,
                    "callback" => $callback,
                    "permission_callback" => function () {
                        return true;
                    }
                ]);
            });
    }

    public function html($html){
        return "$html";
    }

    public function json($data)
    {
        return json_encode($data);
    }
}