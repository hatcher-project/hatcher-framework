<?php
/**
 * @license see LICENSE
 */



use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response\SapiEmitter;

$composer = include __DIR__ . '/../../../vendor/autoload.php';

$GLOBALS['composer'] = $composer;
$GLOBALS['applicationSample'] = __DIR__ . '/../app';

/* @var $application \Hatcher\Application */
$application = include __DIR__ . '/../app/application.php';

$request = ServerRequestFactory::fromGlobals();

$response = $application->routeHttpRequest($request);
(new SapiEmitter())->emit($response);
