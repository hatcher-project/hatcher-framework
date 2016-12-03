<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Hatcher\Application;
use Hatcher\ApplicationSegment;
use Hatcher\DefaultApplication\DefaultDI;
use Hatcher\DirectoryDi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractModule extends ApplicationSegment
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

    public function getNotFoundHandler()
    {
        return [
            '_action' => 'not-found',
            '_route'  => '&:notfound'
        ];
    }

    public function getErrorHandler()
    {
        return [
            '_action' => 'error',
            '_route'  => '&:error'
        ];
    }

    abstract public function routeHttpRequest(ServerRequestInterface $request): ResponseInterface;
}
