<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Aura\Router\Route;
use Hatcher\Exception\NotFound;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action handled by the default RouteHandler
 *
 * @property RouterInterface $router
 */
class Action
{

    /**
     * @var DI
     */
    private $di;
    private $request;
    private $route;
    private $initDone = false;

    final public function init(ServerRequestInterface $request, Route $route)
    {

        if (true == $this->initDone) {
            throw new Exception('Action was already initialized. Action should only be initialized once.');
        }

        $this->request = $request;
        $this->route = $route;
        $this->initDone = true;
    }

    /**
     * @return ResponseInterface|string|array
     */
    public function execute()
    {
        return '';
    }

    public function getDi()
    {
        return $this->di;
    }

    public function __get($name)
    {
        return $this->di->get($name);
    }

    public function notFound()
    {
        throw new NotFound();
    }
}
