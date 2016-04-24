<?php
/**
 * @license see LICENSE
 */



use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response\SapiEmitter;
use \Hatcher\Exception\NoRouteMatchException;

$composer = include __DIR__ . '/../../../vendor/autoload.php';

$GLOBALS['composer'] = $composer;
$GLOBALS['applicationSample'] = __DIR__ . '/../app';

/* @var $application \Hatcher\Application */
$application = include __DIR__ . '/../app/application.php';

$request = ServerRequestFactory::fromGlobals();

try {
    $response = $application->routeHttpRequest($request);
} catch (NoRouteMatchException $e) {
    if ($application->isDev()) {
        throw $e;
    } else {
        // SHOW 404
    }
}

(new SapiEmitter())->emit($response);
