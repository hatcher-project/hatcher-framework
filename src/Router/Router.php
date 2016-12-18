<?php
/**
 * Slim Framework (http://slimframework.com)
 *
 * @link      https://github.com/slimphp/Slim
 * @copyright Copyright (c) 2011-2015 Josh Lockhart
 * @license   https://github.com/slimphp/Slim/blob/3.x/LICENSE.md (MIT License)
 */
namespace Hatcher\Router;

use FastRoute\DataGenerator;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use FastRoute\RouteParser;
use FastRoute\RouteParser\Std as StdParser;
use Hatcher\Config\ConfigProcessor;
use Hatcher\Exception\NotFound;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

/**
 * Router
 *
 * This class organizes Slim application route objects. It is responsible
 * for registering route objects, assigning names to route objects,
 * finding routes that match the current HTTP request, and creating
 * URLs for a named route.
 */
class Router implements RouterInterface
{
    /**
     * Parser
     *
     * @var \FastRoute\RouteParser
     */
    private $routeParser;

    /**
     * @var string
     */
    private $routesFilePath;

    /**
     * Base path used in pathFor()
     *
     * @var string
     */
    protected $basePath = '';

    /**
     * Named routes
     *
     * @var []
     */
    private $routes = null;

    /**
     * @var \FastRoute\Dispatcher
     */
    protected $dispatcher;

    /**
     * @param string|array $routesDef
     */
    public function __construct($routesDef)
    {
        if (is_string($routesDef)) {
            $this->routesFilePath = $routesDef;
        } elseif (is_array($routesDef)) {
            $this->routes = $routesDef;
        } else {
            throw new InvalidArgumentException(
                'Invalid argument for router, expecting a file path or an array of routes'
            );
        }
    }

    /**
     * @return RouteParser
     */
    private function getRouteParser()
    {
        if (!$this->routeParser) {
            $this->routeParser = new StdParser();
        }
        return $this->routeParser;
    }

    public function getRoutesLit()
    {
        if (null === $this->routes) {
            $routes = new ConfigProcessor($this->routesFilePath);
            $this->routes = $routes->all();
        }
        return $this->routes;
    }

    private function getDispatcher()
    {
        if (!$this->dispatcher) {
            $this->dispatcher = \FastRoute\cachedDispatcher(function (RouteCollector $r) {
                foreach ($this->getRoutesLit() as $name => $route) {
                    $route['name'] = $name;
                    $r->addRoute($route['methods'] ?? ['GET'], $route['path'], $route);
                    // TODO warn for invalid route patter
                }
            }, [
                'routeParser' => $this->getRouteParser(),
                'cacheDisabled' => true, // TODO
                'cacheFile' => '/tmp/TODO:HATCHER_ROUTER.cache'
            ]);
        }
        return $this->dispatcher;
    }

    /**
     * Set the base path used in pathFor()
     *
     * @param string $basePath
     *
     */
    public function setBasePath(string $basePath)
    {
        $this->basePath = $basePath;
    }



    /**
     * Dispatch router for HTTP request
     *
     * @param  ServerRequestInterface $request The current HTTP request object
     *
     * @return array
     *
     * @link   https://github.com/nikic/FastRoute/blob/master/src/Dispatcher.php
     */
    public function match(ServerRequestInterface $request) : MatchedRoute
    {
        $virtualPath = $this->extractRequestVirtualPath($request);

        if (empty($virtualPath) || $virtualPath{0} !== '/') {
            $virtualPath = '/' . $virtualPath;
        }

        $dispatchData = $this->getDispatcher()->dispatch(
            $request->getMethod(),
            $virtualPath
        );

        return $this->prepareMatchingData($dispatchData);
    }

    private function prepareMatchingData(array $dispatchData) : MatchedRoute
    {
        if ($dispatchData[0] === 0) {
            throw new NotFound;
        }
        $data = array_merge(
            $dispatchData[1]['defaults'] ?? [],
            $dispatchData[2]
        );
        return new MatchedRoute($dispatchData[1]['name'], $data);
    }

    private function extractRequestVirtualPath(ServerRequestInterface $request)
    {
        $params = $request->getServerParams();
        $requestScriptName = isset($params['SCRIPT_NAME']) ? $params['SCRIPT_NAME'] : null;
        $requestUri = $request->getUri()->getPath();
        if ($requestScriptName) {
            $virtualPath = $requestUri;
            $basePath = null;
            $requestScriptDir = dirname($requestScriptName);
            if (stripos($requestUri, $requestScriptName) === 0) {
                $basePath = $requestScriptName;
            } elseif ($requestScriptDir !== '/' && stripos($requestUri, $requestScriptDir) === 0) {
                $basePath = $requestScriptDir;
            }

            if ($basePath) {
                $virtualPath = ltrim(substr($requestUri, strlen($basePath)), '/');
            }

            return $virtualPath;
        } else {
            return $requestUri;
        }
    }

    /**
     * Get named route object
     *
     * @param string $name        Route name
     *
     * @return array
     *
     * @throws RuntimeException   If named route does not exist
     */
    public function getNamedRoute($name)
    {
        $routes = $this->getRoutesLit();

        if (!isset($routes[$name])) {
            throw new RuntimeException('Named route does not exist for name: ' . $name);
        }
        return $routes[$name];
    }

    /**
     * Build the path for a named route excluding the base path
     *
     * @param string $name        Route name
     * @param array  $data        Named argument replacement data
     * @param array  $queryParams Optional query string parameters
     *
     * @return string
     *
     * @throws RuntimeException         If named route does not exist
     * @throws InvalidArgumentException If required data not provided
     */
    public function relativePathFor($name, array $data = [], array $queryParams = [])
    {
        // TODO
        $route = $this->getNamedRoute($name);
        $pattern = $route['path'];

        $routeDatas = $this->routeParser->parse($pattern);
        // $routeDatas is an array of all possible routes that can be made. There is
        // one routedata for each optional parameter plus one for no optional parameters.
        //
        // The most specific is last, so we look for that first.
        $routeDatas = array_reverse($routeDatas);

        $segments = [];
        foreach ($routeDatas as $routeData) {
            foreach ($routeData as $item) {
                if (is_string($item)) {
                    // this segment is a static string
                    $segments[] = $item;
                    continue;
                }

                // This segment has a parameter: first element is the name
                if (!array_key_exists($item[0], $data)) {
                    // we don't have a data element for this segment: cancel
                    // testing this routeData item, so that we can try a less
                    // specific routeData item.
                    $segments = [];
                    $segmentName = $item[0];
                    break;
                }
                $segments[] = $data[$item[0]];
            }
            if (!empty($segments)) {
                // we found all the parameters for this route data, no need to check
                // less specific ones
                break;
            }
        }

        if (empty($segments)) {
            throw new InvalidArgumentException('Missing data for URL segment: ' . $segmentName);
        }
        $url = implode('', $segments);

        if ($queryParams) {
            $url .= '?' . http_build_query($queryParams);
        }

        return $url;
    }


    /**
     * Build the path for a named route including the base path
     *
     * @param string $name        Route name
     * @param array  $data        Named argument replacement data
     * @param array  $queryParams Optional query string parameters
     *
     * @return string
     *
     * @throws RuntimeException         If named route does not exist
     * @throws InvalidArgumentException If required data not provided
     */
    public function pathFor($name, array $data = [], array $queryParams = [])
    {
        $url = $this->relativePathFor($name, $data, $queryParams);

        if ($this->basePath) {
            $url = $this->basePath . $url;
        }

        return $url;
    }
}
