<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Psr\Http\Message\ServerRequestInterface;

return new class extends ModuleAdapter{


    protected $setup = false;

    public function setup(){
        if(!$this->setup){
            $this->module->getDI()->set('router', include __DIR__ . "/services/router.php");
            $this->setup = true;
        }
    }

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
