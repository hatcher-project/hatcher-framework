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
use Symfony\Component\Config\FileLocator;
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
                'cache_dir' => $this->resolvePath('cache/__router'),
                'debug' => $this->application->isDev(),
                'matcher_cache_class' => '__Hatcher_' . $this->name . 'UrlMatcher',
                'generator_cache_class' => '__Hatcher_' . $this->name . 'UrlGenerator'
            ]
        );

        return $router;
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
                $router = $this->getDI()->get('router');
                $virtualPath = $this->extractRequestVirtualPath($request);

                /* @var $router Router */
                $router = $this->getDI()->get('router');
                $match = $router->match($virtualPath);

                return $this->getRouteHandler()->handle($match, $request);
            } catch (ResourceNotFoundException $e) {
                if ($router && $notFoundHandler = $this->getNotFoundHandler()) {
                    return $this->getRouteHandler()->handle($notFoundHandler, $request);
                }
                return new HtmlResponse('Page not found!', 404);
            }
        } catch (\Exception $e) {
            if ($this->application->isDev()) {
                // Whoops will display a nice error message
                throw $e;
            } else {
                if ($router && $errorHandler = $this->getErrorHandler()) {
                    return $this->getRouteHandler()->handle($errorHandler, $request);
                }
                return new EmptyResponse(500);
            }
        }
    }
}
