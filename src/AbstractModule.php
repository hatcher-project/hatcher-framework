<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Hatcher\DefaultApplication\DefaultDI;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractModule extends ApplicationSegment implements ApplicationAwareInterface
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
    public function getApplication() : Application
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

    abstract public function routeHttpRequest(ServerRequestInterface $request): ResponseInterface;
}
