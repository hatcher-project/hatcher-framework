<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\DefaultApplication\Module;

use GuzzleHttp\Psr7\Response;
use Hatcher\DirectoryDi;
use Hatcher\Exception;
use Hatcher\Router\NotFound;
use Hatcher\RouteHandlerInterface;
use Hatcher\Router\MatchedRoute;
use Hatcher\Router\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Module extends \Hatcher\AbstractModule
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

    public function routeHttpRequest(ServerRequestInterface $request): ResponseInterface
    {
        try {
            try {
                /* @var $router Router */
                $router = $this->getDI()->get('router');
                $match = $router->match($request);

            // HANDLE NOT FOUND
            } catch (NotFound $e) {
                if ($router && $notFoundHandler = $this->getNotFoundHandler()) {
                    return $this->getRouteHandler()->handle(new MatchedRoute('&:error', $notFoundHandler), $request);
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
                    return $this->getRouteHandler()->handle(new MatchedRoute('&:error', $errorHandler), $request);
                } else {
                    throw $e;
                }
            }
        }
    }
}
