<?php
/**
 * @license see LICENSE
 */

namespace pho;

use Hatcher\Application;
use Hatcher\Config;
use Hatcher\DI;
use Hatcher\ModuleManager\RegisteredModules;
use Hatcher\ModuleRouter;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Stream;

describe('The application routes a request', function () {


    $generatePSR7Request = function (
        $url,
        $method = 'GET',
        $remoteAddress = '127.0.0.1',
        array $queryData = [],
        array $data = null
    ) {

        $serverParams = ['SERVER_NAME' => $remoteAddress];
        $fileParams = [];
        $body = new Stream('php://memory', 'r+');
        $headers = [];

        if (count($queryData) > 0) {
            $url .= '?' . http_build_query($queryData);
        }
        $request = new ServerRequest(
            $serverParams,
            $fileParams,
            $url,
            $method,
            $body,
            $headers
        );

        if ($data) {
            $request = $request->withQueryParams($queryData);
            if ($data) {
                $request = $request->withParsedBody($data);
            }
        }

        return $request;
    };

    /* @var $application \Hatcher\Application */
    $application = include $GLOBALS['applicationSample'] . '/application.php';

    it('should return pong when calling /ping', function () use ($application, $generatePSR7Request) {
        $request = $generatePSR7Request('/ping', 'GET');
        $response = $application->routeHttpRequest($request);
        expect((string)$response->getBody())->toBe('pong');
    });

    it('should return "hello world" when calling /hello', function () use ($application, $generatePSR7Request) {

        $request = $generatePSR7Request('/hello', 'GET');
        $response = $application->routeHttpRequest($request);

        expect((string)$response->getBody())->toBe('hello world');

    });

    it('should return "home!" when calling /', function () use ($application, $generatePSR7Request) {
        $request = $generatePSR7Request('/', 'GET');
        $response = $application->routeHttpRequest($request);

        expect((string)$response->getBody())->toBe('home!');
    });

    it('should return error 500 when action is not defined correctly', function () use ($application, $generatePSR7Request) {
        $request = $generatePSR7Request('/errored', 'GET');
        $response = $application->routeHttpRequest($request);

        expect($response->getStatusCode())->toBe(500);
        expect((string)$response->getBody())->toBe('error page');
    });


    it('should return error 404 when route does not exist', function () use ($application, $generatePSR7Request) {
        $request = $generatePSR7Request('/thisRouteDoesNotExist', 'GET');
        $response = $application->routeHttpRequest($request);

        expect($response->getStatusCode())->toBe(404);
        expect((string)$response->getBody())->toBe('custom not found');
    });


});
