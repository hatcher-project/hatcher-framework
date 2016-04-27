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
    public function registerModule(string $name, callable $matcher, $loader = 'auto')
    {
        $this->moduleNames[$name]= ['matcher' => $matcher, 'loader' => $loader] ;
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

            $loader = $this->moduleNames[$name]['loader'];
            $path = $this->directory . '/' . $name;

            if ('auto' == $loader) {
                $moduleFile = $path . '/module.php';
                if (file_exists($moduleFile)) {
                    $this->modules[$name] = $this->loadModuleFile($moduleFile);
                } else {
                    $this->modules[$name] = $this->createDefaultModuleByName($name, $path);
                }
            } elseif ($this->moduleNames[$name]['loader'] == 'default') {
                $this->modules[$name] = $this->createDefaultModuleByName($name, $path);
            } else {
                throw new Exception(
                    'Unknown loading method' . $this->moduleNames[$name]['loader'] . ' in module ' . $name
                );
            }
        }
        return $this->modules[$name];
    }

    private function loadModuleFile($file)
    {
        return require $file;
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
            if ($modulDef['matcher']($request)) {
                return $this->getModule($moduleName);
            }
        }
        throw new Exception('No module matched the request');
    }
}
