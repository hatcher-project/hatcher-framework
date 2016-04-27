<?php
/**
 * @license see LICENSE
 */

use \Hatcher\RouterInterface;
use Zend\Diactoros\Response\HtmlResponse;

return function (RouterInterface $router) {

    $router->add('home', '/', [
        'action' => 'index'
    ]);

    $router->add('ping', '/ping');

    $router->add('hello-world', '/hello', 'hello');

    /**
     * this route wont match and will return a 500 http code
     */
    $router->add('errored', '/errored');

    $router->error('error');
    $router->notFound('not-found');

};
