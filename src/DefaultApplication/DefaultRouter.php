<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\DefaultApplication;

use Aura\Router\Route;
use Aura\Router\RouterContainer;
use Hatcher\Exception\NoRouteMatchException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;

class DefaultRouter
{

    /**
     * @var RouterContainer
     */
    protected $routerContainer;

    public function __construct()
    {
        $this->routerContainer = new RouterContainer();
            $this->routerContainer->getMap()->get('ping', '/ping', function (ServerRequestInterface $request) {
                return new HtmlResponse('pong');
            });

            $this->routerContainer->getMap()->get('hello', '/hello', function (ServerRequestInterface $request) {
                return new HtmlResponse('hello world');
            });
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
}
