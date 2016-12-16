<?php
/**
 * @license see LICENSE
 */

namespace pho;

use Hatcher\Application;
use Hatcher\Config;
use Hatcher\DI;
use Hatcher\ModuleRouter;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;

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
        $body = new Stream(fopen('php://memory', 'r+'));
        $headers = [];

        if (count($queryData) > 0) {
            $url .= '?' . http_build_query($queryData);
        }
        $request = new ServerRequest(
            $method,
            $url,
            $headers,
            $body = null,
            '1.1',
            $serverParams
        );

        if ($data) {
            $request = $request->withQueryParams($queryData);
            if ($data) {
                $request = $request->withParsedBody($data);
            }
        }

        return $request;
    };

    putenv('DEV=false');
    /* @var $application \Hatcher\Application */
    $application = include $GLOBALS['applicationSample'] . '/../bootstrap.php';

    $sendRequest = function(ServerRequestInterface $requestInterface) use ($application) {
        try{
            return $application->routeHttpRequest($requestInterface);
        } catch (\Exception $e) {
            die('kkk');
        }
    };


    it('should return pong when calling /ping', function () use ($sendRequest, $generatePSR7Request) {
        $request = $generatePSR7Request('/ping', 'GET');
        $response = $sendRequest($request);

        expect($response->getStatusCode())->toBe(200);
        expect((string)$response->getBody())->toBe('pong');
    });

    it('should return "hello world" when calling /hello', function () use ($application, $generatePSR7Request) {

        $request = $generatePSR7Request('/hello', 'GET');
        $response = $application->routeHttpRequest($request);

        expect($response->getStatusCode())->toBe(200);
        expect((string)$response->getBody())->toBe('hello world');
    });

    it('should return "home!" when calling /', function () use ($application, $generatePSR7Request) {
        $request = $generatePSR7Request('/', 'GET');
        $response = $application->routeHttpRequest($request);

        expect($response->getStatusCode())->toBe(200);
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

        $request = $generatePSR7Request('/request/123', 'GET');
        $response = $application->routeHttpRequest($request);
    });

    it('should read the configuration', function () use ($application, $generatePSR7Request) {
        $request = $generatePSR7Request('/with-config', 'GET');
        $response = $application->routeHttpRequest($request);

        expect($response->getStatusCode())->toBe(200);
        expect((string)$response->getBody())->toBe('a thing');
    });

    it('dynamic routing should detect data', function () use ($application, $generatePSR7Request) {
        $request = $generatePSR7Request('/customer/foo', 'GET');
        $response = $application->routeHttpRequest($request);

        expect($response->getStatusCode())->toBe(200);
        expect((string)$response->getBody())->toBe('Customer: foo');
    });

    it('should execute middlewares when calling /with-middleware', function () use ($application, $generatePSR7Request) {
        $request = $generatePSR7Request('/with-middleware', 'GET');
        $response = $application->routeHttpRequest($request);

        expect($response->getStatusCode())->toBe(200);
        expect((string)$response->getBody())->toBe('Homer is: simpsons');
        expect((string)$response->getHeaderLine('homerValue'))->toBe('simpsons');
        expect((string)$response->getHeaderLine('foobar'))->toBe('baz');
    });

    it('should render view when calling /with-view', function () use ($application, $generatePSR7Request) {
        $request = $generatePSR7Request('/with-view', 'GET');
        $response = $application->routeHttpRequest($request);

        expect($response->getStatusCode())->toBe(200);
        expect((string)$response->getBody())->toBe('[foo from frontend bar from frontend] from frontend');
    });
});
