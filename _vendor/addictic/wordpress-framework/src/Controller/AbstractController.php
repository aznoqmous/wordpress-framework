<?php

namespace Addictic\WordpressFramework\Controller;

abstract class AbstractController
{
    public $namespace;
    public $name;
    public $value;

    public function html($html){
        header('Content-Type: text/html');
        echo "$html";
        exit;
    }

    public function json($data)
    {
        return json_encode($data);
    }
}