<?php

require_once dirname(__DIR__).'/vendor/autoload.php';
use App\Kernel;

$httpRequest = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

$environment = "development";
$debug = $environment == "development";
$_SERVER['APP_ENV'] = $environment;
$_SERVER['APP_DEBUG'] = $debug;
putenv("APP_ENV=$environment");

$kernel = new Kernel($environment, $debug);
$response = $kernel->handle($httpRequest);
$response->send();
if($kernel instanceof \Symfony\Component\HttpKernel\TerminableInterface) $kernel->terminate($httpRequest, $response);

