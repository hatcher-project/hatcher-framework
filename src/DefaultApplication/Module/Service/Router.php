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
        if (!file_exists($module->resolvePath('routes.yml'))) {
            throw new Exception('No routing file found, please provide a routes.yml file');
        }

        $loader = new YamlFileLoader($module);
        $routesFile = 'routes.yml';

        return new SfRouter(
            $loader,
            $routesFile,
            [
                'cache_dir' => $module->getCachePath('router'),
                'debug' => $module->getApplication()->isDev(),
                'matcher_cache_class' =>  '__Hatcher_' . $module->getName() . 'UrlMatcher',
                'generator_cache_class' => '__Hatcher_' . $module->getName() . 'UrlGenerator'
            ]
        );
    }
}
