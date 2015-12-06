<?php

namespace Hatcher;

use Aura\Router\RouterContainer;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;

return function(){
    $router = new RouterContainer();
    $router->getMap()->get("ping", "/ping", function(ServerRequestInterface $request){
        return new HtmlResponse("pong");
    });

    $router->getMap()->get("hello", "/hello", function(ServerRequestInterface $request){
        return new HtmlResponse("hello world");
    });
    return $router;
};
