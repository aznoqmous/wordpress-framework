<?php

namespace Addictic\WordpressFramework\Annotation;

class PostTypeManager extends AbstractManager
{
    protected function addEntity(mixed $instance, mixed $annotation)
    {
        $instance->name = $annotation->name;
        $instance->icon = $annotation->icon ?? $instance->icon;
        $instance->taxonomies = $annotation->taxonomies ?? $instance->taxonomies;
        $instance->priority = $annotation->priority ?? $instance->priority;
        $this->entities[$instance->getName()] = (object)[
            'class' => $instance::class,
            'annotation' => $annotation,
            'instance' => $instance
        ];
    }

    protected function setup()
    {
        uasort($this->entities, fn($a, $b) => $a->priority - $b->priority);
        foreach ($this->entities as $entity) {
            $entity->instance->register();
        }
    }
}