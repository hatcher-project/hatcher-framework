<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\DefaultApplication\Module\Service;

use Hatcher\DefaultApplication\Module\Module;
use Hatcher\Exception;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Router as SfRouter;

class Router
{
    public function __invoke(Module $module)
    {
        return new \Hatcher\Router\Router($module->resolvePath('routes.yml'));
    }
}
