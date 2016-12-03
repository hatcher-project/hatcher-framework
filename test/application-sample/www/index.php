<?php

use \GuzzleHttp\Psr7\ServerRequest;
use function \Hatcher\sendResponse;

/** @var \Hatcher\Application $application */
$application = require __DIR__ . '/../bootstrap.php';

$request = ServerRequest::fromGlobals();
$response = $application->routeHttpRequest($request);

sendResponse($response);
