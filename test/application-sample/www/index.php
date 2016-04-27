<?php

use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response\SapiEmitter;

$application = require __DIR__ . '/../bootstrap.php';

$request = ServerRequestFactory::fromGlobals();
$response = $application->routeHttpRequest($request);
(new SapiEmitter())->emit($response);
