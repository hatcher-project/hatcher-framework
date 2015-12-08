<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

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
     * Register a list of modules in the manager
     * $manager->registerModuleNames("frontend", "backend", "api");
     * @param string ...$modules list of modules
     */
    public function registerModuleNames(array $names)
    {
        $this->moduleNames += $names;
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
            if (!in_array($name, $this->moduleNames)) {
                throw new Exception("No module was registered with the name '$name'");
            }

            $path = "$this->directory" . "/$name";
            $this->modules[$name] = new Module($name, $path, $this->application);
        }
        return $this->modules[$name];
    }

    public function hasModule($name)
    {
        return isset($this->modules[$name]) ?? file_exists("$this->directory" . "/$name");
    }

    /**
     * Get an array containing the module names
     * @return string[]
     */
    public function getModuleNames(): array
    {
        return $this->moduleNames;
    }
}
