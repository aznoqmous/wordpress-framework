<?php

namespace App\PostTypes;

use Addictic\WordpressFrameworkBundle\PostTypes\AbstractPostType;
use Addictic\WordpressFrameworkBundle\PostTypes\Attribute\PostType;
use Symfony\Component\Routing\Attribute\Route;


#[PostType("test", icon:"dashicons-bank", taxonomies:["theme","job"])]
class TestPostType extends AbstractPostType
{
    protected function configure()
    {
        dump("TEST");
    }
}