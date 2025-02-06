<?php

namespace Addictic\WordpressFramework\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Finder\Finder;

abstract class AbstractManager
{
    public static mixed $annotation = PostType::class;
    public AnnotationReader $annotationReader;
    public $entities = [];
    private static $instances = [];

    private function __construct()
    {
        $this->annotationReader = new AnnotationReader();
        if(function_exists("add_action")){
            add_action("after_setup_theme", function(){
                $this->setup();
            });
        }
    }

    public function discover($namespace, $directory, $annotationName)
    {
        $path = $directory;
        $finder = new Finder();
        $finder->files()->in($path);

        foreach ($finder as $file) {
            $class = $namespace . "/" . $file->getRelativePathname();
            $class = preg_replace("/\//", "\\", $class);
            $class = str_replace(".php", "", $class);

            $annotation = $this->annotationReader->getClassAnnotation(new \ReflectionClass($class), $annotationName);
            if ($annotation) $this->addClass($class, $annotation);
            $this->handleMethods($class);
        }
    }

    protected function handleMethods($class){
        $methods = (new \ReflectionClass($class))->getMethods();

        foreach ($methods as $method) {
            $annotations = $this->annotationReader->getMethodAnnotations($method);
            if (!$annotations) continue;

            foreach ($annotations as $annotation) {
                if (!($annotation instanceof Route)) continue;
                $this->addMethod($method, $class, $annotation);
            }

        }
    }

    protected abstract function addClass(string $className, mixed $annotation);
    protected abstract function addMethod(\ReflectionMethod $method, string $className, mixed $annotation);

    protected function register(string $name, mixed $instance)
    {
        $this->entities[$name] = $instance;
    }

    public function get(string $name)
    {
        return isset($this->entities[$name]) ? $this->entities[$name] : null;
    }

    abstract protected function setup();

    public static function getInstance()
    {
        if(!isset(static::$instances[static::class])) static::$instances[static::class] = new static();
        return static::$instances[static::class];
    }
}