<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Hatcher\Application;
use Hatcher\DefaultApplication\Module;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Hatcher\DefaultApplication\Module as DefaultModule;

/**
 * Bundles of modules that allows create modules and to route a request to the good module
 */
class ModuleManager
{
    protected $directory;
    /**
     * @var Application
     */
    protected $application;

    /**
     * @var Module[]
     */
    protected $modules = [];
    protected $moduleNames = [];

    public function __construct($baseDirectory, Application $application)
    {
        $this->directory = $baseDirectory;
        $this->application = $application;
    }

    /**
     * Registers a module in the application
     * @param string $name
     * @param callable $matcher
     */
    public function registerModule(string $name, $matcher)
    {
        $this->moduleNames[$name]= $matcher;
    }


    /**
     * Get a module by its name
     * @param string $name
     * @return Module
     * @throws Exception
     */
    public function getModule(string $name): Module
    {
        if (!isset($this->modules[$name])) {
            if (!isset($this->moduleNames[$name])) {
                throw new Exception(sprintf('No module was registered with the name "%s"', $name));
            }

            $path = $this->directory . '/' . $name;

            $this->modules[$name] = $this->createDefaultModuleByName($name, $path);
        }
        return $this->modules[$name];
    }


    private function createDefaultModuleByName($name, $path)
    {
        return new DefaultModule($name, $path, $this->application);
    }

    public function hasModule($name)
    {
        return isset($this->moduleNames[$name]);
    }

    /**
     * Get an array containing the module names
     * @return string[]
     */
    public function getModuleNames(): array
    {
        return array_keys($this->moduleNames);
    }

    /**
     * Route the request
     * @param ServerRequestInterface $request
     * @return Module
     * @throws Exception
     */
    public function getModuleForRequest(ServerRequestInterface $request): Module
    {
        foreach ($this->moduleNames as $moduleName => $modulDef) {
            if ($modulDef($request)) {
                return $this->getModule($moduleName);
            }
        }
        throw new Exception('No module matched the request');
    }
}
