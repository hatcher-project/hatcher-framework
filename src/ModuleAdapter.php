<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class ModuleAdapter
{

    /**
     * @var Module
     */
    protected $module;

    final public function initializeWith(Module $module)
    {
        $this->module = $module;
    }

    /**
     * Setup the module. That means populate the services, etc...
     * It's called only when needed thus there is no overhead when not used
     */
    abstract public function setup();

    /**
     * Dispatch a request and returns a valid http response
     * @param ServerRequestInterface $request the input request
     * @return ResponseInterface the http response to send back
     */
    abstract public function dispatchRequest(ServerRequestInterface $request);

    /**
     * Check if the given request is dispatchable by the module
     * @param ServerRequestInterface $request
     * @return mixed
     */
    abstract public function requestIsValid(ServerRequestInterface $request);
}
