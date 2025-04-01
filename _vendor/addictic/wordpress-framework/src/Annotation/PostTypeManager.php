<?php

namespace Addictic\WordpressFramework\Annotation;

class PostTypeManager extends AbstractManager
{
    protected function addClass(string $className, mixed $annotation)
    {
        $instance = new $className();
        $instance->name = $annotation->name;
        $instance->icon = $annotation->icon ?? $instance->icon;
        $instance->taxonomies = $annotation->taxonomies ?? $instance->taxonomies;
        $instance->priority = $annotation->priority ?? $instance->priority;
        $this->entities[] = (object)[
            'class' => $instance::class,
            'annotation' => $annotation,
            'instance' => $instance
        ];
    }

    protected function setup()
    {
        $entities = [];
        uasort($this->entities, fn($a, $b) => $a->instance->priority - $b->instance->priority);
        foreach ($this->entities as $entity) {
            $entities[$entity->instance->getName()] = $entity;
        }
        $this->entities = $entities;
        foreach($this->entities as $entity) {
            $entity->instance->register();
        }
    }

    protected function addMethod(\ReflectionMethod $method, string $className, mixed $annotation)
    {
    }
}