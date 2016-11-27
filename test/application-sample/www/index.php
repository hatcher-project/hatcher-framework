<?php

use Zend\Diactoros\ServerRequestFactory;
use function \Hatcher\sendResponse;

/** @var \Hatcher\Application $application */
$application = require __DIR__ . '/../bootstrap.php';

$request = ServerRequestFactory::fromGlobals();
$response = $application->routeHttpRequest($request);

sendResponse($response);
