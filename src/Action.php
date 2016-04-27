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
     * @var AbstractModule
     */
    protected $module;

    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * @var Route
     */
    protected $route;

    private $initDone = false;

    final public function init(ServerRequestInterface $request, Route $route, AbstractModule $module)
    {
        if (true == $this->initDone) {
            throw new Exception('Action was already initialized. Action should only be initialized once.');
        }

        $this->request = $request;
        $this->route = $route;
        $this->module = $module;
        $this->initDone = true;
    }

    /**
     * @return ResponseInterface|string|array
     */
    public function execute()
    {
        return '';
    }

    public function __get($name)
    {
        return $this->module->getDI()->get($name);
    }

    public function notFound()
    {
        throw new NotFound();
    }
}
