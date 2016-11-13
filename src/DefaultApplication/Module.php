<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\DefaultApplication;

use Aura\Router\Route;
use Hatcher\Application;
use Hatcher\ApplicationSegment;
use Hatcher\DI;
use Hatcher\DirectoryDi;
use Hatcher\Exception\NotFound;
use Hatcher\RouteHandlerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Hatcher\AbstractModule as BaseModule;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Diactoros\Response\HtmlResponse;

class Module extends \Hatcher\AbstractModule
{

    private $routeHandler;

    public function __construct($moduleName, $directory, \Hatcher\Application $application)
    {
        $di = new DirectoryDi($directory . '/services', [$this]);

        $di->set('router', function (BaseModule $module) {
            $router = new Router();
            call_user_func(require $module->resolvePath('routes.php'), $router);
            return $router;
        });
        parent::__construct($moduleName, $directory, $application, $di);
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
        if($requestScriptName){
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
        }else{
            return $requestUri;
        }
    }

    public function dispatchRequest(ServerRequestInterface $request): ResponseInterface
    {
        $router = null;
        try {
            /* @var $router Router */
            $router = $this->getDI()->get('router');

            try {
                $virtualPath = $this->extractRequestVirtualPath($request);

                if($virtualPath !== $request->getUri()->getPath()){
                    $uri = $request->getUri()->withPath($virtualPath);
                    $match = $router->match($request->withUri($uri));
                } else {
                    $match = $router->match($request);
                }

                return $this->getRouteHandler()->handle($match, $request);
            } catch (NotFound $e) {
                if ($router && $notFoundHandler = $router->getNotFoundHandler()) {
                    $notFoundRoute = new Route();
                    $notFoundRoute->handler($notFoundHandler);
                    return $this->getRouteHandler()->handle($notFoundRoute, $request);
                }
                return new HtmlResponse('Page not found!', 404);
            }
        } catch (\Exception $e) {
            if ($this->application->isDev()) {
                // Whoops will display a nice error message
                throw $e;
            } else {
                if ($router && $errorHandler = $router->getErrorHandler()) {
                    $errorRoute = new Route();
                    $errorRoute->handler($errorHandler);
                    return $this->getRouteHandler()->handle($errorRoute, $request);
                }
                return new EmptyResponse(500);
            }
        }
    }
}
