<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\DefaultApplication;

use Hatcher\Application;
use Hatcher\ApplicationSegment;
use Hatcher\DI;
use Hatcher\DirectoryDi;
use Hatcher\Exception;
use Hatcher\Exception\NotFound;
use Hatcher\RouteHandlerInterface;
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
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Diactoros\Response\HtmlResponse;

class Module extends \Hatcher\AbstractModule
{

    private $routeHandler;

    public function __construct($moduleName, $directory, \Hatcher\Application $application)
    {
        $di = new DirectoryDi($directory . '/services', [$this]);

        $di->set('router', function (BaseModule $module) {
            return $this->generateRouter();
        });
        parent::__construct($moduleName, $directory, $application, $di);
    }

    public function getCachePath($path)
    {
        $cache = $this->application->getCacheDirectory();
        return $cache . '/module/' . $this->getName() . '/' . $path;
    }

    private function generateRouter()
    {
        if (file_exists($this->resolvePath('routes.yml'))) {
            $loader = new YamlFileLoader($this);
            $routesFile = 'routes.yml';
        } else {
            throw new Exception('No routing file found, please provide a routes.yml file');
        }

        $router = new Router(
            $loader,
            $routesFile,
            [
                'cache_dir' => $this->getCachePath('router'),
                'debug' => $this->application->isDev(),
                'matcher_cache_class' => $this->getMatcherCacheClass(),
                'generator_cache_class' => '__Hatcher_' . $this->name . 'UrlGenerator'
            ]
        );

        return $router;
    }

    private function getMatcherCacheClass()
    {
        return '__Hatcher_' . $this->getName() . 'UrlMatcher';
    }

    public function getNotFoundHandler()
    {
        return [
            '_action' => 'not-found',
            '_route'  => '&:notfound'
        ];
    }

    public function getErrorHandler()
    {
        return [
            '_action' => 'error',
            '_route'  => '&:error'
        ];
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

    public function dispatchRequest(ServerRequestInterface $request): ResponseInterface
    {

        /* @var $router Router */

        try {
            try {
                $virtualPath = $this->extractRequestVirtualPath($request);

                if ($virtualPath{0} !== '/') {
                    $virtualPath = '/' . $virtualPath;
                }

                $safePath = base64_encode($virtualPath);
                $cacheFile = $this->getCachePath(
                    'router/routes/' . substr($safePath, 0, 2)  . '/' . $safePath{2}  . '/' . $safePath . '.route.match'
                );

                $cache = new ConfigCache($cacheFile, $this->application->isDev());

                if ($cache->isFresh()) {
                    $match = unserialize(file_get_contents($cacheFile));
                } else {
                    /* @var $router Router */
                    $router = $this->getDI()->get('router');
                    $match = $router->match($virtualPath);

                    $routeCache = $this->getCachePath('router/' . $this->getMatcherCacheClass() . '.php');

                    $cache->write(serialize($match), [new FileResource($routeCache)]);
                }

                return $this->getRouteHandler()->handle($match, $request);

            // HANDLE NOT FOUND
            } catch (ResourceNotFoundException $e) {
                if ($router && $notFoundHandler = $this->getNotFoundHandler()) {
                    return $this->getRouteHandler()->handle($notFoundHandler, $request);
                }
                return new HtmlResponse('Page not found!', 404);
            }

        // HANDLE ERRORS
        } catch (\Exception $e) {
            if ($this->application->isDev()) {
                // Whoops will display a nice error message
                throw $e;
            } else {
                if ($errorHandler = $this->getErrorHandler()) {
                    return $this->getRouteHandler()->handle($errorHandler, $request);
                }
                return new EmptyResponse(500);
            }
        }
    }
}
