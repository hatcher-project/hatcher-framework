<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\ModuleManager;

use Hatcher\Application;
use Hatcher\DefaultApplication\Module\Module as DefaultModule;
use Hatcher\DefaultApplication\Module\Module;
use Hatcher\Exception;
use Psr\Http\Message\ServerRequestInterface;

class ApplicationModuleManager implements ModuleManagerInterface
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

        $this->moduleNames = $application->getInitialisationValue('modules') ?? [];
    }


    public function getModule(string $name) : Module
    {
        if (!isset($this->modules[$name])) {
            if (!isset($this->moduleNames[$name])) {
                throw new Exception(sprintf('No module was registered with the name "%s"', $name));
            }

            $path = $this->directory . '/' . $name;

            $this->modules[$name] = new DefaultModule($name, $path, $this->application);
        }
        return $this->modules[$name];
    }

    public function hasModule(string $name) : bool
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
            if (!isset($modulDef['matcher']['function'])) {
                throw new Exception('Unable to find module matcher. Module matcher is not defined correctly');
            }

            $match = call_user_func_array(
                $modulDef['matcher']['function'],
                [$request] + $modulDef['matcher']['data']
            );

            if ($match) {
                return $this->getModule($moduleName);
            }
        }
        throw new Exception('No module matched the request');
    }
}
