<?php

namespace Addictic\WordpressFramework\Annotation;

class BlockManager extends AbstractManager
{
    protected function addClass(string $className, mixed $annotation)
    {
        $instance = new $className($annotation->name, $annotation->template);
        $this->entities[$annotation->name] = (object)[
            'class' => $instance::class,
            'annotation' => $annotation,
            'instance' => $instance
        ];
    }

//    protected function setup()
//    {
//        uasort($this->entities, fn($a, $b) => $a->instance->priority - $b->instance->priority);
//        foreach ($this->entities as $entity) {
//            $entity->instance->register();
//        }
//    }

    protected function setup()
    {
    }

    protected function addMethod(\ReflectionMethod $method, string $className, mixed $annotation)
    {
    }
}