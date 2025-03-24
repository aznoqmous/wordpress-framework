<?php

namespace Addictic\WordpressFramework\Annotation;

class TaxonomyManager extends AbstractManager
{

    protected function addClass(string $className, mixed $annotation)
    {
        $instance = new $className();
        $instance->name = $annotation->name;
        $instance->priority = $annotation->priority ?? $instance->priority;
        $this->entities[$instance->getName()] = (object)[
            'class' => $instance::class,
            'annotation' => $annotation,
            'instance' => $instance
        ];
    }

    protected function setup()
    {
        uasort($this->entities, fn($a, $b) => $a->instance->priority - $b->instance->priority);
        foreach ($this->entities as $entity) {
            $entity->instance->register();
        }
    }

    protected function addMethod(\ReflectionMethod $method, string $className, mixed $annotation)
    {
    }
}