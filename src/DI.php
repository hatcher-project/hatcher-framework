<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Pimple\Container;

class DI
{


    /**
     * @var Container
     */
    protected $container;

    public function __construct()
    {
        $this->container = new Container();
    }


    /**
     * get a service from the di
     * @param $what
     * @return mixed the service object
     */
    public function get($what)
    {
        return $this->container[$what];
    }

    /**
     * add a service to the di
     * @param $what
     * @param callable $callable
     */
    public function set($what, callable $callable)
    {
        $this->container[$what] = $callable;
    }
}
