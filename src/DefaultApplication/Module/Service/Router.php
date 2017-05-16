<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\DefaultApplication\Module\Service;

use Hatcher\DefaultApplication\Module\Module;
use Hatcher\Router\ConfigFileRouter;

class Router
{
    public function __invoke(Module $module)
    {
        return new ConfigFileRouter($module->resolvePath('routes.yml'));
    }
}
