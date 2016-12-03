<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Hatcher\Exception\InvalidResponse;
use Hatcher\Exception\NotFound;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use function GuzzleHttp\Psr7\stream_for;

/**
 * Action handled by the default RouteHandler
 *
 */
abstract class Action
{

    /**
     * @var AbstractModule
     */
    protected $module;

    /**
     * @var Application
     */
    protected $application;

    /**
     * @var array
     */
    protected $data;

    private $initDone = false;

    final public function init(array $route, AbstractModule $module)
    {
        if (true == $this->initDone) {
            throw new Exception('Action was already initialized. Action should only be initialized once.');
        }

        $this->data = $route;
        $this->module = $module;
        $this->application = $module->getApplication();
        $this->initDone = true;
    }

    final public function __invoke(ServerRequestInterface $request) : ResponseInterface
    {
        $actionResponse = $this->execute($request);
        return $this->parseResponse($actionResponse);
    }

    /**
     * Parse the response into a response instance
     * @param $response
     * @param ResponseInterface $originalResponse
     * @return \Psr\Http\Message\MessageInterface
     * @throws InvalidResponse
     */
    private function parseResponse($response) : ResponseInterface
    {
        if (null === $response) {
            return $this->module->getDI()->get('response');
        } elseif (is_string($response)) {
            return $this->module->getDI()->get('response')->withBody(stream_for($response));
        } elseif ($response instanceof ResponseInterface) {
            return $response;
        } elseif (is_array($response)) {
            $data = json_encode($response);
            $response = $this->module->getDI()->get('response')->withBody(stream_for($data));
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            $typeError = null;
            if (is_object($response)) {
                $typeError = 'Instance of class ' . get_class($response);
            } else {
                $typeError = var_export($typeError, true);
            }
            throw new InvalidResponse(
                "Invalid response from controller. $typeError returned but expected instance of ResponseInterface"
            );
        }
    }

    /**
     * @return ResponseInterface|string|array
     */
    abstract public function execute(ServerRequestInterface $request);

    public function __get($name)
    {
        return $this->module->getDI()->get($name);
    }

    public function notFound()
    {
        throw new NotFound();
    }


    public function middlewares()
    {
        return [];
    }
}
