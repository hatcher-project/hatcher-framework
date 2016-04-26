<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\DefaultApplication;

use Aura\Router\Route;
use Aura\Router\RouterContainer;
use Hatcher\Exception\NoRouteMatchException;
use Hatcher\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;

class DefaultRouter implements RouterInterface
{

    /**
     * @var RouterContainer
     */
    protected $routerContainer;

    public function __construct()
    {
        $this->routerContainer = new RouterContainer();
    }

    /**
     * @param ServerRequestInterface $request
     * @return Route
     */
    public function match(ServerRequestInterface $request): Route
    {
        $match = $this->routerContainer->getMatcher()->match($request);
        if (!$match) {
            throw new NoRouteMatchException('No route matched the request');
        }
        return $match;
    }

    public function add($name, $path, $data): Route
    {
        return $this->routerContainer->getMap()->route(
            $name,
            $path,
            function (ServerRequestInterface $request) use ($data): ResponseInterface
            {
                return call_user_func($data);
            }
        );
    }
}
