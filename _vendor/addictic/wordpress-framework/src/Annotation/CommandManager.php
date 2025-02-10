<?php

namespace Addictic\WordpressFramework\Annotation;

class CommandManager extends AbstractManager
{
    public static mixed $annotation = Command::class;

    protected function addClass(string $className, mixed $annotation)
    {
        $instance = new $className($annotation->name);
        $this->entities[$annotation->name] = (object)[
            'class' => $instance::class,
            'annotation' => $annotation,
            'instance' => $instance
        ];
    }

    protected function addMethod(\ReflectionMethod $method, string $className, mixed $annotation)
    {
        $instance = new $className($annotation->name);
        $this->entities[$annotation->name] = (object)[
            'class' => $instance::class,
            'annotation' => $annotation,
            'instance' => $instance,
            'method' => $method
        ];
    }

    protected function setup()
    {
    }

    public function run($command, $arguments)
    {
        if(!isset($this->entities[$command])) throw new \Exception("Command \"$command\" doesnt exists.");
        echo "Running command '$command'\n";
        if(property_exists($this->entities[$command], "method")) $this->entities[$command]->instance->{$this->entities[$command]->method->name}(...$arguments);
        else $this->entities[$command]->instance->__invoke(...$arguments);
    }
}