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
        $router = $module->getDI()->get("router");
        $match = $router->getMatcher()->match($request);

        if(!$match){
            throw new NoRouteMatchException("No route matched the request");
        }

        return ($match->handler)($request);
    }

    public function requestIsValid(Module $module, ServerRequestInterface $request): bool
    {
        $addr = $request->getServerParams()["REMOTE_ADDR"];
        return $addr == "front.hatcher.test" || $addr == "127.0.0.1";
    }

};
