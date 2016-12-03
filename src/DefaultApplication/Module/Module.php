<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\DefaultApplication\Module;

use GuzzleHttp\Psr7\Response;
use Hatcher\Application;
use Hatcher\ApplicationSegment;
use Hatcher\DefaultApplication\Module\RouteHandler;
use Hatcher\DI;
use Hatcher\DirectoryDi;
use Hatcher\Exception;
use Hatcher\Exception\NotFound;
use Hatcher\RouteHandlerInterface;
use Interop\Http\Middleware\DelegateInterface;
use Interop\Http\Middleware\ServerMiddlewareInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Hatcher\AbstractModule as BaseModule;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Router;

class Module extends \Hatcher\AbstractModule implements ServerMiddlewareInterface
{

    private $routeHandler;

    public function __construct($moduleName, $directory, \Hatcher\Application $application)
    {
        $di = new DirectoryDi($directory . '/services', [$this]);
        parent::__construct($moduleName, $directory, $application, $di);
    }

    public function getCachePath($path)
    {
        $cache = $this->application->getCacheDirectory();
        return $cache . '/module/' . $this->getName() . '/' . $path;
    }

    /**
     * @return RouteHandlerInterface
     */
    protected function getRouteHandler()
    {
        if (null == $this->routeHandler) {
            $this->routeHandler = new RouteHandler($this);
        }
        return $this->routeHandler;
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

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        return $this->routeHttpRequest($request);
    }

    public function routeHttpRequest(ServerRequestInterface $request): ResponseInterface
    {
        try {
            try {
                $virtualPath = $this->extractRequestVirtualPath($request);

                if ($virtualPath{0} !== '/') {
                    $virtualPath = '/' . $virtualPath;
                }

                /* @var $router Router */
                $router = $this->getDI()->get('router');
                $match = $router->match($virtualPath);

            // HANDLE NOT FOUND
            } catch (ResourceNotFoundException $e) {
                if ($router && $notFoundHandler = $this->getNotFoundHandler()) {
                    return $this->getRouteHandler()->handle($notFoundHandler, $request);
                }
                return new Response(404, [], \GuzzleHttp\Psr7\stream_for('Page not found!'));
            }

            return $this->getRouteHandler()->handle($match, $request);

        // HANDLE ERRORS
        } catch (\Exception $e) {
            if ($this->application->isDev()) {
                // Whoops will display a nice error message
                throw $e;
            } else {
                if ($errorHandler = $this->getErrorHandler()) {
                    return $this->getRouteHandler()->handle($errorHandler, $request);
                } else {
                    throw $e;
                }
            }
        }
    }
}
