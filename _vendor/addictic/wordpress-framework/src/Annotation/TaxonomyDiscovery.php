<?php

namespace Addictic\WordpressFramework\Annotation;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Finder\Finder;

class TaxonomyDiscovery
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

    public function getTaxonomies()
    {
        if (!$this->entities) $this->discoverTaxonomies();
        return $this->entities;
    }

    private function discoverTaxonomies()
    {
        $path = $this->rootDir . '/../' . $this->directory;
        $finder = new Finder();
        $finder->files()->in($path);

        foreach ($finder as $file) {
            $class = $this->namespace . '\\' . $file->getBasename('.php');
            $annotation = $this->annotationReader->getClassAnnotation(new \ReflectionClass($class), "Addictic\WordpressFramework\Annotation\Taxonomy");

            if (!$annotation) {
                continue;
            }

            $instance = new $class();
            $instance->name = $annotation->name;
            $instance->register();
            $this->entities[$instance->name] = [
                'class' => $class,
                'annotation' => $annotation,
                'instance' => $instance
            ];
        }
    }

}