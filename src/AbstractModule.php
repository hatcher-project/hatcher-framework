<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Hatcher\Application;
use Hatcher\ApplicationSegment;
use Hatcher\DefaultApplication\DefaultDI;
use Hatcher\DirectoryDi;
use Interop\Http\Middleware\DelegateInterface;
use Interop\Http\Middleware\ServerMiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractModule extends ApplicationSegment implements ServerMiddlewareInterface
{

    /**
     * @var Application
     */
    protected $application;

    /**
     * @var string
     */
    protected $name;


    public function __construct(string $moduleName, string $directory, Application $application, DI $di)
    {
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

    public function setApplication($application)
    {
        $this->application = $application;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        return $this->routeHttpRequest($request);
    }

    abstract public function routeHttpRequest(ServerRequestInterface $request): ResponseInterface;
}
