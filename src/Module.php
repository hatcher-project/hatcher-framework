<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Hatcher\Application;
use Hatcher\Config\ConfigFactory;
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
        $di = new DirectoryDi($directory . "/services");
        $this->adapter = include $directory . "/module.php";
        //$options = $this->adapter->getOptions();


        parent::__construct($directory, $di);
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

    public function dispatchRequest(ServerRequestInterface $request)
    {
        return $this->adapter->dispatchRequest($this, $request);
    }
}
