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
    protected static $instance;

    private function __construct()
    {
        $this->annotationReader = new AnnotationReader();
        static::$instance = $this;

        add_action("after_setup_theme", function(){
            $this->setup();
        });
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

            if (!$annotation) continue;

            $this->addEntity(new $class(), $annotation);
        }
    }

    protected abstract function addEntity(mixed $instance, mixed $annotation);
    protected function register(string $name, mixed $instance)
    {
        $this->entities[$name] = $instance;
    }

    abstract protected function setup();

    public static function getInstance():static
    {
        if(!static::$instance) new static();
        return static::$instance;
    }
}