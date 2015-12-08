<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Psr\Http\Message\ServerRequestInterface;

return new class extends ModuleAdapter{



    public function dispatchRequest(ServerRequestInterface $request){
        /* @var $router \Aura\Router\RouterContainer */
        $this->setup();
        $router = $this->module->getDI()->get("router");
        $match = $router->getMatcher()->match($request);

        return ($match->handler)($request);
    }

    public function requestIsValid(ServerRequestInterface $request){
        return $request->getServerParams()["REMOTE_ADDR"] == "front.hatcher.test";
    }

};
