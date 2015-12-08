<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

return new class extends ModuleAdapter{

    public function dispatchRequest(Module $module, ServerRequestInterface $request): ResponseInterface
    {
        /* @var $router \Aura\Router\RouterContainer */
        $router = $module->getDI()->get("router");
        $match = $router->getMatcher()->match($request);

        return ($match->handler)($request);
    }

    public function requestIsValid(Module $module, ServerRequestInterface $request): bool
    {
        return $request->getServerParams()["REMOTE_ADDR"] == "front.hatcher.test";
    }

};
