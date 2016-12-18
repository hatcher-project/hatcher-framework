<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\DefaultApplication\Module;

use Hatcher\AbstractModule;
use Hatcher\Action;
use Hatcher\Exception;
use Hatcher\RouteHandlerInterface;
use Hatcher\Router\MatchedRoute;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use function GuzzleHttp\Psr7\stream_for;

/**
 * Takes route parameters returned from the router and executes the matching action
 */
class RouteHandler implements RouteHandlerInterface
{

    /**
     * @var AbstractModule
     */
    protected $module;

    /**
     * RouteHandler constructor.
     * @param AbstractModule $module
     */
    public function __construct(AbstractModule $module)
    {
        $this->module = $module;
    }


    public function handle(MatchedRoute $route, ServerRequestInterface $request): ResponseInterface
    {

        if (!isset($route['_action'])) {
            $route['_action'] = $route->getRouteName();
        }

        $action = $this->getAction($route['_action'], $route, $request);
        return $this->executeAction($action, $route, $request);
    }

    /**
     * Find the action instance
     * @param string $actionName
     * @param array $route
     * @param ServerRequestInterface $request
     * @return Action
     * @throws Exception
     */
    private function getAction(
        string $actionName,
        MatchedRoute $route,
        ServerRequestInterface $request
    ) : Action {

        $actionFile = $this->module->resolvePath('actions/' . $actionName . '.php');
        if (!file_exists($actionFile)) {
            throw new Exception('Action file does not exist: ' . $actionFile);
        }

        $action = require $actionFile;

        if (!($action instanceof Action)) {
            throw new Exception(
                'action ' . $route['data']['action'] . ' is not an instance of Hatcher\Action.' .
                ' using the class: ' . get_class($action)
            );
        }

        return $action;
    }

    /**
     * Executes the action, handling middleware
     * @param Action $action
     * @param array $route
     * @param ServerRequestInterface $request
     * @return mixed|ResponseInterface
     * @throws Exception
     */
    private function executeAction(Action $action, MatchedRoute $route, ServerRequestInterface $request)
    {
        try {
            $action->init($route, $this->module);
            $stack = $action->middlewares();
            $stack[] = $action;

            return $this->executeMiddlewareStack($stack, $request);
        } catch (Exception\NotFound $e) {
            return $this->handle($this->module->getNotFoundHandler(), $request);
        }
    }

    private function executeMiddlewareStack($stack, ServerRequestInterface $request)
    {
        $f = $this->makeNext(0, $stack);
        return call_user_func($f, $request);
    }

    private function makeNext($current, $stack)
    {
        if ($current >= count($stack)) {
            return function (ServerRequestInterface $request) {
                throw new Exception('request stack has been consumed without response');
            };
        }
        return function (ServerRequestInterface $request) use ($current, $stack) : ResponseInterface {
            return call_user_func($stack[$current], $request, $this->makeNext($current + 1, $stack));
        };
    }
}
