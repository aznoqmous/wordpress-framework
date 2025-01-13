<?php

namespace Addictic\WordpressFramework\Annotation;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Finder\Finder;

class BlockDiscovery
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

    public function getBlocks()
    {
        if (!$this->entities) $this->discoverBlocks();
        return $this->entities;
    }

    private function discoverBlocks()
    {
        $path = $this->rootDir . '/../' . $this->directory;
        $finder = new Finder();
        $finder->files()->in($path);


        foreach ($finder as $file) {
            $class = $this->namespace . '\\' . $file->getRelativePathname();
            $class = str_replace(".php", "", $class);
            $annotation = $this->annotationReader->getClassAnnotation(new \ReflectionClass($class), "Addictic\WordpressFramework\Annotation\Block");

            if (!$annotation) {
                continue;
            }

            $instance = new $class($annotation->name, $annotation->template);

            $this->entities[$annotation->name] = [
                'class' => $class,
                'annotation' => $annotation,
                'instance' => $instance
            ];
        }
    }

}