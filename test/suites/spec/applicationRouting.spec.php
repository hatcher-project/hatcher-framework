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

/**
 * @param $url
 * @param string $method
 * @param string $remoteAddress
 * @param array $queryData
 * @param array|null $data
 * @return \Psr\Http\Message\ServerRequestInterface|ServerRequest
 */


describe('The application routes a request', function () {


    $generatePSR7Request = function (
        $url,
        $method = "GET",
        $remoteAddress = "127.0.0.1",
        array $queryData = [],
        array $data = null
    ) {

        $serverParams = ["REMOTE_ADDR" => $remoteAddress];
        $fileParams = [];
        $body = new Stream("php://memory", "r+");
        $headers = [];

        if (count($queryData) > 0) {
            $url .= "?" . http_build_query($queryData);
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

    /* @var $application Application */
    $application = include __DIR__ . "/../../application-sample/application.php";

    it('should return pong when calling /ping', function () use ($application, $generatePSR7Request) {

        $request = $generatePSR7Request("/ping", "GET", "front.hatcher.test");

        /* @var $moduleManager \Hatcher\ModuleManagerInterface */
        $moduleManager = $application->getDI()->get("moduleManager");

        $moduleRouter = new ModuleRouter($moduleManager);
        $module = $moduleRouter->dispatchRequest($request);
        $response = $module->getAdapter()->dispatchRequest($request);

        expect((string)$response->getBody())->toBe("pong");

    });

    it('should return "hello world" when calling /hello', function () use ($application, $generatePSR7Request) {

        $request = $generatePSR7Request("/hello", "GET", "front.hatcher.test");

        /* @var $moduleManager \Hatcher\ModuleManagerInterface */
        $moduleManager = $application->getDI()->get("moduleManager");

        $moduleRouter = new ModuleRouter($moduleManager);
        $module = $moduleRouter->dispatchRequest($request);
        $response = $module->getAdapter()->dispatchRequest($request);

        expect((string)$response->getBody())->toBe("hello world");

    });

});
