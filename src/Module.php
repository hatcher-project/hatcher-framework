<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Module extends ApplicationSegment
{

    /**
     * @var Application
     */
    protected $application;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var ModuleAdapter
     */
    protected $adapter;

    public function __construct(string $moduleName, string $directory, Application $application)
    {
        parent::__construct($directory, $config, new DI());
        $this->application = $application;
        $this->name = $moduleName;
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @return string the name of the module
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return ModuleAdapter
     */
    public function getAdapter()
    {

        if(!$this->adapter){

        }

        return $this->adapter;
    }
}
