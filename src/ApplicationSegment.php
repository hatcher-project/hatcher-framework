<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

/**
 * Represents a segment of the application: an object
 * that contains a config and a service locator (DI)
 *
 * @property Config $config
 *
 */
class ApplicationSegment
{

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var DI
     */
    protected $di;

    public function __construct(Config $config, DI $di)
    {
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
     * The application configuration
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Provide shortcut to get config object or services
     */
    function __get($name)
    {
        if('config' == $name){
            return $this->getConfig();
        }else{
            return  $this->getDI()->get($name);
        }
    }

}
