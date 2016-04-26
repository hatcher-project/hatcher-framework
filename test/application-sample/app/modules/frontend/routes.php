<?php
/**
 * @license see LICENSE
 */

use \Hatcher\RouterInterface;
use Zend\Diactoros\Response\HtmlResponse;

return function (RouterInterface $router) {

    $router->add('home', '/', function () {
        return new HtmlResponse('Home');
    });

    $router->add('ping', '/ping', function () {
        return new HtmlResponse('pong');
    });

    $router->add('hello', '/hello', function () {
        return new HtmlResponse('hello world');
    });

};
