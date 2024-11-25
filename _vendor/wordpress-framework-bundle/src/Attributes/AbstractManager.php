<?php

namespace Addictic\WordpressFrameworkBundle\Attributes;

use ReflectionClass;
use Symfony\Component\Finder\Finder;

abstract class AbstractManager
{
    protected static $attribute;
    private static $instance;

    protected $instances = [];

    private function __construct()
    {
        static::$instance = $this;
    }

    public static function getInstance(): AbstractManager
    {
        if (!static::$instance) static::$instance = new static();
        return static::$instance;
    }


    public function add($path, $namespace = "App")
    {
        $classes = $this->loadClassesWithAttribute(static::$attribute, $path, $namespace);
        return $this;
    }

    private function loadClassesWithAttribute($attributeName, $path, $namespace)
    {
        if (!is_dir($path)) return;
        $finder = new Finder();
        $finder->files()->in($path);
        foreach ($finder as $file) {
            $class = $namespace . "/" . $file->getRelativePathname();
            $class = preg_replace("/\//", "\\", $class);
            $class = str_replace(".php", "", $class);
            try {
                $reflectionClass = new ReflectionClass($class);
                foreach ($reflectionClass->getAttributes() as $attribute) {
                    $instance = new $class();
                    $this->instances[$class] = $instance;
                    $this->create($instance, $attribute);
                }
            } catch (\Exception $exception) {
            }
        }
    }

    abstract protected function create($instance, $attribute);
    abstract public function register();
}