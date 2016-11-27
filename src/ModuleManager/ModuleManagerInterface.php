<?php

/**
 * @license see LICENSE
 */

namespace Hatcher\ModuleManager;

use Hatcher\DefaultApplication\Module\Module;
use Psr\Http\Message\ServerRequestInterface;

interface ModuleManagerInterface
{

    public function getModule(string $name) : Module;

    public function hasModule(string $name) : bool;

    public function getModuleNames() : array;

    public function getModuleForRequest(ServerRequestInterface $request) : Module;
}
