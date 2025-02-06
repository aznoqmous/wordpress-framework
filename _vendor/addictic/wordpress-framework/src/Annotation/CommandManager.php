<?php

namespace Addictic\WordpressFramework\Annotation;

class CommandManager extends AbstractManager
{

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
    }

    protected function setup()
    {
    }

    public function run($command, $arguments)
    {
        if(!isset($this->entities[$command])) throw new \Exception("Command \"$command\" doesnt exists.");
        echo "Running command '$command'\n";
        $this->entities[$command]->instance->__invoke(...$arguments);
    }
}