<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Hatcher\Application;
use Hatcher\ApplicationSegment;
use Hatcher\Config\ConfigFactory;
use Hatcher\DefaultApplication\DefaultDI;
use Hatcher\DirectoryDi;
use Hatcher\Exception\NoRouteMatchException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class Module extends ApplicationSegment
{

    /**
     * @var Application
     */
    protected $application;

    /**
     * @var string
     */
    protected $name;


    public function __construct(string $moduleName, string $directory, Application $application, DI $di = null)
    {

        if (null == $di) {
            $di = new DefaultDI($directory . '/services', [$this]);
        }

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

    abstract public function dispatchRequest(ServerRequestInterface $request): ResponseInterface;
}
