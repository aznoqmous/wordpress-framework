<?php

namespace Addictic\WordpressFramework\Controller;

abstract class AbstractController
{
    public $namespace;
    public $name;
    public $value;

    public function html($html){
        return "$html";
    }

    public function json($data)
    {
        return json_encode($data);
    }
}