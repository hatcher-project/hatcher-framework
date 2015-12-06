<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Hatcher\ModuleManager\ModuleDoesNotExistException;

interface ModuleManagerInterface
{

    /**
     * Get a module by its name
     * @throws ModuleDoesNotExistException
     * @return Module
     */
    public function getModule($name);

    /**
     * Get all the available modules
     * @return Module[]
     */
    public function getModules();
}
