<?php

namespace Addictic\WordpressFrameworkBundle\Helpers;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Container
{
    private static ContainerBuilder $instance;
    public static function get($service)
    {
        if (!isset(static::$instance)) {
            static::$instance = new ContainerBuilder();
            $loader = new YamlFileLoader(static::$instance, new FileLocator(__DIR__ . "/../../config"));
            $loader->load("services.yaml");
        }
        return static::$instance->get($service);
    }
}