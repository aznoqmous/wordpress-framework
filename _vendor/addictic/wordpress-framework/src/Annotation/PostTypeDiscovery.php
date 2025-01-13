<?php

namespace Addictic\WordpressFramework\Annotation;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Finder\Finder;

class PostTypeDiscovery
{
    private $namespace;
    private $directory;
    private $annotationReader;
    private $rootDir;
    private $entities = [];

    public function __construct($namespace, $directory, $rootDir, Reader $annotationReader)
    {
        $this->namespace = $namespace;
        $this->annotationReader = $annotationReader;
        $this->directory = $directory;
        $this->rootDir = $rootDir;
    }

    public function getPostTypes()
    {
        if (!$this->entities) $this->discoverPostTypes();
        return $this->entities;
    }

    private function discoverPostTypes()
    {
        $path = $this->rootDir . '/../' . $this->directory;
        $finder = new Finder();
        $finder->files()->in($path);

        $instances = [];

        foreach ($finder as $file) {
            $class = $this->namespace . "\\" . $file->getRelativePathname();
            $class = preg_replace("/\//", "\\", $class);
            $class = str_replace(".php", "", $class);
            $annotation = $this->annotationReader->getClassAnnotation(new \ReflectionClass($class), PostType::class);

            if (!$annotation) {
                continue;
            }

            $instance = new $class();
            $instance->name = $annotation->name;
            $instance->icon = $annotation->icon ?? $instance->icon;
            $instance->taxonomies = $annotation->taxonomies ?? $instance->taxonomies;
            $instance->priority = $annotation->priority ?? $instance->priority;
            $this->entities[$instance->getName()] = [
                'class' => $class,
                'annotation' => $annotation,
                'instance' => $instance
            ];

            $instances[] = $instance;
        }

        uasort($instances, fn($a,$b)=> $a->priority - $b->priority);
        foreach ($instances as $instance){
            $instance->register();
        }

    }

}