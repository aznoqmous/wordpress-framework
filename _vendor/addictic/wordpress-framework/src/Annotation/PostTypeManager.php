<?php

namespace Addictic\WordpressFramework\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;

class PostTypeManager
{
    protected static PostTypeManager $instance;
    private PostTypeDiscovery $discovery;

    public function __construct()
    {
        $this->discovery = new PostTypeDiscovery("\\Addictic\\WordpressFramework\\PostTypes", "PostTypes", __DIR__, new AnnotationReader());
        static::$instance = $this;
    }

    public function getPostTypes()
    {
        return $this->discovery->getPostTypes();
    }

    public function getPostType($name)
    {
        $entities = $this->discovery->getPostTypes();
        if (isset($entities[$name])) {
            return (object) $entities[$name];
        }

        throw new \Exception('Worker not found.');
    }

    public function create($name)
    {
        $entities = $this->discovery->getPostTypes();
        if (array_key_exists($name, $entities)) {
            $class = $entities[$name]['class'];
            if (!class_exists($class)) {
                throw new \Exception('Worker class does not exist.');
            }
            return new $class();
        }

        throw new \Exception('Worker does not exist.');
    }

    public static function getInstance():PostTypeManager
    {
        return static::$instance;
    }
}