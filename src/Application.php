<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use \Composer\Autoload\ClassLoader;
use Hatcher\ApplicationSegment;
use Hatcher\Config;
use Hatcher\DirectoryDi;
use Hatcher\Exception;
use Hatcher\ModuleManager;
use Psr\Http\Message\ServerRequestInterface;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run as WhoopsRun;

/**
 * @property Config $config
 * @property ModuleManager $moduleManager
 */
class Application extends ApplicationSegment
{

    /**
     * @var ClassLoader
     */
    protected $classLoader;

    /**
     * @var bool
     */
    protected $dev;



    public function __construct(string $directory, ClassLoader $classLoader, array $options = [])
    {
        $di = new DirectoryDi($directory . "/services", [$this]);
        parent::__construct($directory, $di);
        $this->dev = (bool)($options["dev"] ?? true);
        $this->classLoader = $classLoader;

        $this->registerErrorHandler();
    }


    /**
     * The application is running in dev environment
     * @return bool
     */
    public function isDev()
    {
        return $this->dev === true;
    }

    /**
     * The application classLoader from composer. Aims to dynamically register module paths
     * @return ClassLoader
     */
    public function getClassLoader()
    {
        return $this->classLoader;
    }

    public function routeHttpRequest(ServerRequestInterface $request)
    {
        $module = $this->moduleManager->getModuleForRequest($request);
        return $module->dispatchRequest($request);
    }

    protected function registerErrorHandler()
    {
        $run     = new WhoopsRun;
        $handler = new PrettyPageHandler;
        $run->pushHandler($handler);
        $run->register();

    }
}
