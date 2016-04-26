<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\DefaultApplication;

use Hatcher\Application;
use Hatcher\ApplicationSegment;
use Hatcher\Config\ConfigFactory;
use Hatcher\DI;
use Hatcher\DirectoryDi;
use Hatcher\Exception\NoRouteMatchException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Hatcher\Module as BaseModule;

class Module extends \Hatcher\Module
{

    public function __construct($moduleName, $directory, \Hatcher\Application $application)
    {
        $di = new DirectoryDi($this->resolvePath('services'), [$this]);

        $di->set('router', function (BaseModule $module) {
            $router = new DefaultRouter();
            call_user_func(require $module->resolvePath('routes.php'), $router);
            return $router;
        });


        parent::__construct($moduleName, $directory, $application, $di);
    }


    public function dispatchRequest(ServerRequestInterface $request): ResponseInterface
    {
        /* @var $router DefaultRouter */
        $router = $this->getDI()->get('router');
        $match = $router->match($request);
        return call_user_func($match->handler, $request);
    }
}
