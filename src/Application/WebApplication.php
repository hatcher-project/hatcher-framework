<?php

namespace Hatcher\Application;

/**
 * @license see LICENSE
 */
class WebApplication extends \Hatcher\Application
{
    public function __construct(
        $rootPath,
        \Hatcher\Config $config,
        \Composer\Autoload\ClassLoader $classLoader,
        \Hatcher\DI $di,
        $devMode = false
    ) {
        parent::__construct($rootPath, $config, $classLoader, $di, $devMode); // TODO: Change the autogenerated stub
    }
}
