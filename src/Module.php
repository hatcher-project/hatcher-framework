<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class Module extends ApplicationSegment
{

    /**
     * @var Application
     */
    protected $application;

    public function __construct(Application $application, $rootPath, Config $config)
    {
        parent::__construct($rootPath, $config, new DI());
        $this->application = $application;
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Check if the request is valid for the module and returns true in case it is
     *
     * A good use case example is to match each module with a domain name and check if the domain correspond to
     * the module
     *
     * When the application dispatch the request between modules, only the first module returning true will be used
     *
     * @param ServerRequestInterface $request
     * @return bool true if the request is accepted by the module
     */
    abstract public function requestMatches(ServerRequestInterface $request);

    /**
     * Dispatch the request in the module. The module is intended to return a valid response
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Exception if an error happens during the dispatching and the module cant return a valid response
     */
    abstract public function dispatch(ServerRequestInterface $request);
}
