<?php

/**
 * @license see LICENSE
 */

namespace Hatcher\ModuleManager;

use Hatcher\Module;
use Hatcher\ModuleManagerInterface;

class RegisteredModules implements ModuleManagerInterface
{

    /**
     * @var Module[]
     */
    protected $modules = [];

    public function registerModule(Module $module)
    {
        $this->modules[$module->getName()] = $module;
    }

    /**
     * @inheritdoc
     */
    public function getModule($name)
    {
        if (!isset($this->modules[$name])) {
            throw new ModuleDoesNotExistException($name);
        } else {
            return $this->modules[$name];
        }
    }

    /**
     * @inheritdoc
     */
    public function getModules()
    {
        return $this->modules;
    }
}
