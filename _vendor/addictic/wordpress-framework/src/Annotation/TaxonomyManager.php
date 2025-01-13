<?php

namespace Addictic\WordpressFramework\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;

class TaxonomyManager
{
    protected static TaxonomyManager $instance;
    private TaxonomyDiscovery $discovery;

    public function __construct()
    {
        $this->discovery = new TaxonomyDiscovery("\\Addictic\\WordpressFramework\\Taxonomies", "Taxonomies", __DIR__, new AnnotationReader());
        static::$instance = $this;
    }

    public function getTaxonomies()
    {
        return $this->discovery->getTaxonomies();
    }

    public function getTaxonomy($name)
    {
        $entitiess = $this->discovery->getTaxonomies();
        if (isset($entitiess[$name])) {
            return $entitiess[$name]['instance'];
        }

        throw new \Exception('Worker not found.');
    }

    public function create($name)
    {
        $entities = $this->discovery->getTaxonomies();
        if (array_key_exists($name, $entities)) {
            $class = $entities[$name]['class'];
            if (!class_exists($class)) {
                throw new \Exception('Worker class does not exist.');
            }
            return new $class();
        }

        throw new \Exception('Worker does not exist.');
    }

    public static function getInstance():TaxonomyManager
    {
        return static::$instance;
    }
}