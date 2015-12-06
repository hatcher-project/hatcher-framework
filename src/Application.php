<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use \Composer\Autoload\ClassLoader;

class Application extends ApplicationSegment
{

    /**
     * @var ClassLoader
     */
    protected $classLoader;

    /**
     * @var bool
     */
    protected $dev;

    public function __construct(
        Config $config,
        ClassLoader $classLoader,
        DI $di,
        $devMode = false
    ) {
        parent::__construct($config, $di);
        $this->dev = $devMode;
        $this->classLoader = $classLoader;
    }

    /**
     * The application is running in dev environment
     * @return bool
     */
    public function isDev()
    {
        return $this->dev === true;
    }

    /**
     * The application classLoader from composer. Aims to dynamically register module paths
     * @return ClassLoader
     */
    public function getClassLoader()
    {
        return $this->classLoader;
    }
}
