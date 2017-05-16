<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\Router;

use FastRoute\DataGenerator;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use FastRoute\RouteParser;
use FastRoute\RouteParser\Std as StdParser;
use Hatcher\Router\NotFound;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

/**
 * A router built on the top of FastRoute
 *
 * This router was largely inspired by the slim framework one
 *
 */
abstract class Router
{
    /**
     * Parser
     *
     * @var \FastRoute\RouteParser
     */
    private $routeParser;

    /**
     * Base path used in pathFor()
     *
     * @var string
     */
    protected $basePath = '';

    protected $cacheFile;

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

    public function setCacheFile($cacheFile)
    {
        $this->cacheFile = $cacheFile;
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

    public function getRoutesList()
    {
        if (null === $this->routes) {
            $this->routes = $this->loadRouteArray();
        }
        return $this->routes;
    }

    /**
     * implement to get routes to be cached
     */
    abstract protected function loadRouteArray();

    private function getDispatcher()
    {
        if (!$this->dispatcher) {
            $cache = $this->cacheFile ? $this->cacheFile : '';

            $this->dispatcher = \FastRoute\cachedDispatcher(function (RouteCollector $r) {
                foreach ($this->getRoutesList() as $name => $route) {
                    $route['name'] = $name;

                    $methods = isset($route['methods']) ? $route['methods'] : ['GET'];

                    $r->addRoute($methods, $route['path'], $route);
                    // TODO warn for invalid route patter
                }
            }, [
                'routeParser' => $this->getRouteParser(),
                'cacheFile' => $cache,
                'cacheDisabled' => empty($cache)
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
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * Dispatch router for HTTP request
     *
     * @param  ServerRequestInterface $request The current HTTP request object
     *
     * @return MatchedRoute
     *
     * @link https://github.com/nikic/FastRoute/blob/master/src/Dispatcher.php
     */
    public function match(ServerRequestInterface $request)
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

    /**
     * @param array $dispatchData
     * @return MatchedRoute
     * @throws \Hatcher\Router\NotFound
     */
    private function prepareMatchingData(array $dispatchData)
    {
        if ($dispatchData[0] === 0) {
            throw new NotFound;
        }

        $default = isset($dispatchData[1]['defaults']) && is_array($dispatchData[1]['defaults'])
            ? $dispatchData[1]['defaults'] : [];

        $data = array_merge(
            $default,
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
        $routes = $this->getRoutesList();
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
