<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

/**
 * Represents a segment of the application: an object with sources directory that is clearly located with a path and
 * that contains a config and a service locator (DI)
 */
abstract class ApplicationSegment
{

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var DI
     */
    protected $di;

    /**
     * @var string
     */
    protected $rootPath;


    public function __construct($rootPath, Config $config, DI $di)
    {
        $this->rootPath = $rootPath;
        $this->config = $config;
        $this->di = $di;
    }

    /**
     * The application DI
     * @return DI
     */
    public function getDI()
    {
        return $this->di;
    }

    /**
     * Find a path from the application root
     * @param string|null $path
     * @return string
     */
    public function resolvePath($path = null)
    {
        if ($path) {
            return $this->rootPath . "/" . $path;
        } else {
            return $this->rootPath;
        }
    }

    /**
     * The application configuration
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }
}
