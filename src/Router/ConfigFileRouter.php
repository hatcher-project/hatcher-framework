<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\Router;

use Hatcher\Config\ConfigProcessor;

class ConfigFileRouter extends Router
{
    protected $routerFile;

    /**
     * ConfigFileRouter constructor.
     * @param $routerFile
     */
    public function __construct($routerFile)
    {
        $this->routerFile = $routerFile;
    }


    protected function loadRouteArray()
    {
        $routes = new ConfigProcessor($this->routerFile);
        return $routes->all();
    }
}
