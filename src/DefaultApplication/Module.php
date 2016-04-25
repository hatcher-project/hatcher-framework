<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\DefaultApplication;

use Hatcher\Application;
use Hatcher\ApplicationSegment;
use Hatcher\Config\ConfigFactory;
use Hatcher\DirectoryDi;
use Hatcher\Exception\NoRouteMatchException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Module extends \Hatcher\Module
{

    public function dispatchRequest(ServerRequestInterface $request): ResponseInterface
    {
        /* @var $router DefaultRouter */
        $router = $this->getDI()->get('router');
        $match = $router->match($request);
        return call_user_func($match->handler, $request);
    }
}
