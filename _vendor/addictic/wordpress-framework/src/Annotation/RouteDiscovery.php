<?php

namespace Addictic\WordpressFramework\Annotation;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Finder\Finder;

class RouteDiscovery
{
    private $namespace;
    private $directory;
    private $annotationReader;
    private $rootDir;
    private $routes = [];

    public function __construct($namespace, $directory, $rootDir, Reader $annotationReader)
    {
        $this->namespace = $namespace;
        $this->annotationReader = $annotationReader;
        $this->directory = $directory;
        $this->rootDir = $rootDir;
    }

    public function getRoutes()
    {
        if (!$this->routes) $this->discoverRoutes();
        return $this->routes;
    }

    private function discoverRoutes()
    {
        $path = $this->rootDir . '/../Controller';
        $finder = new Finder();
        $finder->files()->in($path);

        foreach ($finder as $file) {
            $class = $this->namespace . '\\' . $file->getBasename('.php');
            $methods = (new \ReflectionClass($class))->getMethods();

            foreach ($methods as $method) {
                $annotations = $this->annotationReader->getMethodAnnotations($method);
                if (!$annotations) continue;

                foreach ($annotations as $annotation) {
                    if (!($annotation instanceof Route)) continue;
                    $this->registerAnnotation($annotation, $method, $class);
                }

            }
        }
    }

    private function registerAnnotation($annotation, \ReflectionMethod $method, $class)
    {
        $instance = new $class($annotation);
        $instance->value = $annotation->value ?? $instance->value;
        $instance->namespace = $annotation->namespace ?? $instance->namespace;
        $instance->name = $annotation->name ?? $instance->name;
        $methods = $annotation->methods ?? "GET";

        $this->routes[$instance->name] = $annotation->value;

        $route = $instance->value;

        preg_match_all("/\{([^\}]*?)\}/", $route, $matches);

        $methodArgs = array_map(function($param) { return $param->name; }, $method->getParameters());

        $routeArgs = [];
        foreach($matches[0] as $i => $selector){
            $key = $matches[1][$i];
            $route = str_replace($selector, "(?P<{$key}>\S+)", $route);
            $routeArgs[] = $key;
        }

        foreach($routeArgs as $arg){
            if(!in_array($arg, $methodArgs)) throw new \Exception("Definition of Route $annotation->value is missing the $arg argument inside its method definition");
        }
        foreach($methodArgs as $arg){
            if(!in_array($arg, $routeArgs)) throw new \Exception("Definition of Route $annotation->value is missing the $arg argument inside its annotation");
        }


        add_action("rest_api_init",
            function () use ($instance, $method, $methods, $route, $methodArgs) {
                register_rest_route(
                    $instance->namespace,
                    $route, [
                    "methods" => $methods,
                    "callback" => function(\WP_REST_Request $request)use($instance,$method, $methodArgs){
                        /* Handle wpml lang is wp-json api */
                        $language = wpml_get_default_language();
                        if(isset($_SERVER["HTTP_REFERER"])){
                            preg_match("/\/([a-z]{2})\//", $_SERVER["HTTP_REFERER"], $matches);
                            if($matches) $language = $matches[1];
                        }
                        do_action( 'wpml_switch_language', $language);

                        $params = $request->get_params();
                        $args = [];
                        foreach($methodArgs as $key){
                            $args[$key] = $params[$key];
                        }
                        return $instance->{$method->name}(...array_values($args));
                    },
                    "permission_callback" => function () {
                        return true;
                    }
                ]);
            });
    }

}