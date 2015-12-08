<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Hatcher\Application\DefaultConfig as ApplicationDefaultConfig;
use Hatcher\Config\ConfigFactory;


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
     * @var ConfigFactory
     */
    protected $configFactory;

    /**
     * @var DI
     */
    protected $di;

    /**
     * @var string
     */
    protected $directory;



    public function __construct(string $directory, DI $di, ConfigFactory $configFactory)
    {
        $this->directory = $directory;
        $this->di = $di;
        $this->configFactory = $configFactory;
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
        if(!$this->config){
            $this->config = $this->configFactory->read();
        }
        return $this->config;
    }

    /**
     * Find a path from the application root
     * @param string|null $path
     * @return string
     */
    public function resolvePath($path = null)
    {
        if ($path) {
            return $this->directory . "/" . $path;
        } else {
            return $this->directory;
        }
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
