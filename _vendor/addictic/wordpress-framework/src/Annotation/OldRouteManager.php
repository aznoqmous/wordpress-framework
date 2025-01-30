<?php

namespace Addictic\WordpressFramework\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;

class OldRouteManager
{
    private RouteDiscovery $discovery;

    public function __construct()
    {
        $this->discovery = new RouteDiscovery("\\Addictic\\WordpressFramework\\Controller", "Controller", __DIR__, new AnnotationReader());
    }

    public function getRoutes()
    {
        return $this->discovery->getRoutes();
    }

    public function getRoute($name)
    {
        $entitiess = $this->discovery->getRoutes();
        if (isset($entitiess[$name])) {
            return $entitiess[$name];
        }

        throw new \Exception('Worker not found.');
    }

    public function create($name) {
        $entities = $this->discovery->getRoutes();
        if (array_key_exists($name, $entities)) {
            $class = $entities[$name]['class'];
            if (!class_exists($class)) {
                throw new \Exception('Worker class does not exist.');
            }
            return new $class();
        }

        throw new \Exception('Worker does not exist.');
    }
}