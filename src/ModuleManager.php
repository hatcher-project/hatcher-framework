<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Hatcher\ModuleManager\ModuleDoesNotExistException;

class ModuleManager
{
    protected $directory;
    /**
     * @var Application
     */
    protected $application;

    public function __construct($baseDirectory, Application $application)
    {
        $this->directory = $baseDirectory;
        $this->application = $application;
    }

    /**
     * @var Module[]
     */
    protected $modules = [];

    /**
     * @inheritdoc
     */
    public function getModule($name)
    {
        if (!isset($this->modules[$name])) {
            $path = "$this->directory" . "/$name";
            $this->modules[$name] = new Module($name, $path, $this->application);
        }

        return $this->modules[$name];

    }

    public function hasModule($name)
    {
        return isset($this->modules[$name]) ?? file_exists("$this->directory" . "/$name");
    }
}
