<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\DefaultApplication;

use Aura\Router\Route;
use Aura\Router\RouterContainer;
use Hatcher\Exception\InvalidResponse;
use Hatcher\Exception\NotFound;
use Hatcher\RouteHandlerInterface;
use Hatcher\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;

class Router implements RouterInterface
{

    /**
     * @var RouterContainer
     */
    protected $routerContainer;
    protected $notFound;
    protected $error;

    public function __construct()
    {
        $this->routerContainer = new RouterContainer();
    }

    /**
     * @param ServerRequestInterface $request
     * @return Route
     */
    public function match(ServerRequestInterface $request) : Route
    {
        $match = $this->routerContainer->getMatcher()->match($request);
        if (!$match) {
            throw new NotFound;
        }
        return $match;
    }

    public function add($name, $path, $data = null): Route
    {
        return $this->routerContainer->getMap()->route(
            $name,
            $path,
            $data
        );
    }

    public function notFound($data)
    {
        $this->notFound = $data;
    }

    public function error($data)
    {
        $this->error = $data;
    }

    /**
     * @return mixed
     */
    public function getErrorHandler()
    {
        return $this->error;
    }
    /**
     * @return mixed
     */
    public function getNotFoundHandler()
    {
        return $this->notFound;
    }
}
