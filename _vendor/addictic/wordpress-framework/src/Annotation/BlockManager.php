<?php

namespace Addictic\WordpressFramework\Annotation;

use Addictic\WordpressFramework\Blocks\AbstractBlock;
use Doctrine\Common\Annotations\AnnotationReader;

class BlockManager
{
    protected static BlockManager $instance;
    private BlockDiscovery $discovery;

    public function __construct()
    {
        $this->discovery = new BlockDiscovery("\\Addictic\\WordpressFramework\\Blocks", "Blocks", __DIR__, new AnnotationReader());
        static::$instance = $this;
    }

    public function getBlocks()
    {
        return $this->discovery->getBlocks();
    }

    public function getBlock($name)
    {

        $entities = $this->discovery->getBlocks();

        if (isset($entities[$name])) {
            return (object) $entities[$name];
        }
        throw new \Exception('Worker not found.');
    }

    public function create($name)
    {
        $entities = $this->discovery->getBlocks();
        if (array_key_exists($name, $entities)) {
            $class = $entities[$name]['class'];
            if (!class_exists($class)) {
                throw new \Exception('Worker class does not exist.');
            }
            return new $class();
        }

        throw new \Exception('Worker does not exist.');
    }

    public static function getInstance():BlockManager
    {
        return static::$instance;
    }
}