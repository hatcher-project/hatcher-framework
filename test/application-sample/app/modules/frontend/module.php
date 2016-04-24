<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Hatcher\Exception\NoRouteMatchException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

return new class extends ModuleAdapter{

    public function dispatchRequest(Module $module, ServerRequestInterface $request): ResponseInterface
    {
        /* @var $router \Aura\Router\RouterContainer */
        $router = $module->getDI()->get('router');
        $match = $router->getMatcher()->match($request);

        if (!$match) {
            throw new NoRouteMatchException('No route matched the request');
        }

        return call_user_func($match->handler, $request);
    }

};
