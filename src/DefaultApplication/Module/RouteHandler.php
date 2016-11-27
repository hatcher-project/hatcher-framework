<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\DefaultApplication\Module;

use Aura\Router\Route;
use Hatcher\Action;
use Hatcher\Exception;
use Hatcher\Exception\InvalidResponse;
use Hatcher\AbstractModule;
use Hatcher\RouteHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;

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


    public function handle(array $route, ServerRequestInterface $request): ResponseInterface
    {

        if (!isset($route['_route'])) {
            $route['&:unknown'];
            if (!isset($route['_action'])) {
                throw new Exception('Unable to dispatch the given route');
            }
        } elseif (!isset($route['_action'])) {
            $route['_action'] = $route['_route'];
        }

        $response = $this->dispatchAction($route, $request);

        if ($response instanceof ResponseInterface) {
            return $response;
        } elseif (is_array($response)) {
            return new JsonResponse($response);
        } elseif (is_string($response)) {
            return new HtmlResponse($response);
        } else {
            $typeError = null;
            if (null == $response) {
                $typeError = 'NULL';
            } elseif (is_object($response)) {
                $typeError = 'Instance of class ' . get_class($response);
            } else {
                $typeError = var_export($typeError, true);
            }
            throw new InvalidResponse(
                "Invalid response from controller. $typeError returned but expected instance of ResponseInterface"
            );
        }
    }

    private function dispatchAction(array $route, ServerRequestInterface $request)
    {
        if (!isset($route['_action'])) {
            throw new Exception('No action found in the route ' . $route['_route']);
        }

        $actionFile = $this->module->resolvePath('actions/' . $route['_action'] . '.php');
        if (!file_exists($actionFile)) {
            throw new Exception('Action file does not exist: ' . $actionFile);
        }

        $action = require $actionFile;

        if (!($action instanceof Action)) {
            throw new Exception(
                'action ' . $route['_action'] . ' is not an instance of Hatcher\Action.' .
                ' using the class: ' . get_class($action)
            );
        }

        $action->init($request, $route, $this->module);
        return $action->execute();
    }
}
