<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Hatcher\Exception\NotFound;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action handled by the default RouteHandler
 *
 */
class Action
{

    /**
     * @var AbstractModule
     */
    protected $module;

    /**
     * @var Application
     */
    protected $application;

    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * @var array
     */
    protected $data;

    private $initDone = false;

    final public function init(ServerRequestInterface $request, array $route, AbstractModule $module)
    {
        if (true == $this->initDone) {
            throw new Exception('Action was already initialized. Action should only be initialized once.');
        }

        $this->request = $request;
        $this->data = $route;
        $this->module = $module;
        $this->application = $module->getApplication();
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
