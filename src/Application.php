<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use \Composer\Autoload\ClassLoader;
use Hatcher\ApplicationSegment;
use Hatcher\Config;
use Hatcher\DefaultApplication\Whoops\HtmlSafeHandler;
use Hatcher\DirectoryDi;
use Hatcher\Exception;
use Hatcher\ModuleManager;
use Interop\Http\Middleware\DelegateInterface;
use Interop\Http\Middleware\ServerMiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run as WhoopsRun;
use Hatcher\ModuleManager\ModuleManagerInterface;
use Hatcher\ModuleManager\ApplicationModuleManager;

class Application extends ApplicationSegment implements ApplicationAwareInterface
{

    /**
     * @var ClassLoader
     */
    protected $classLoader;

    /**
     * @var bool
     */
    protected $dev;

    /**
     * @var string name of the environment ("production", "devel"...)
     */
    protected $env;

    /**
     * @var ModuleManagerInterface
     */
    protected $moduleManager;

    /**
     * @var array
     */
    protected $initialisationValues;

    protected $cacheDirectory;


    /**
     * @param string $directory
     * @param ClassLoader $classLoader
     * @param array $options list of application initialisation options
     * Possible options:
     * - dev: true to enable dev mode and profiling (default to false)
     * - env: environment such as "production", "devel"... (default to "production")
     */
    public function __construct(string $directory, ClassLoader $classLoader, array $options = [])
    {
        $this->dev = (bool) ($options['dev'] ?? false);
        $this->env = (string) ($options['env'] ?? 'production');
        $this->classLoader = $classLoader;

        $di = new DirectoryDi($directory . '/services', [$this]);
        parent::__construct($directory, $di);

        $this->registerErrorHandler();

        $this->cacheDirectory = $this->resolvePath('cache/_app');

        $this->init();
    }

    /**
     * Loads initialization file
     * @throws \Hatcher\Exception
     */
    private function init()
    {
        $initFile = $this->resolvePath('application.php');

        if (file_exists($initFile)) {
            $applicationInit = require $initFile;

            if (is_callable($applicationInit)) {
                $applicationInit = call_user_func($applicationInit, $this);
            }

            if (is_array($applicationInit)) {
                $this->initialisationValues = $applicationInit;
            } else {
                throw new Exception('Application initialisation file is not valid');
            }
        }
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
     * Application env (dev/production...)
     * @return string
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * @inheritdoc
     */
    public function getApplication() : Application
    {
        return $this;
    }

    /**
     * Cache directory for application internal cache
     * @return string
     */
    public function getCacheDirectory()
    {
        return $this->cacheDirectory;
    }

    /**
     * The application classLoader from composer. Aims to dynamically register module paths
     * @return ClassLoader
     */
    public function getClassLoader(): ClassLoader
    {
        return $this->classLoader;
    }

    /**
     * Get the module manager the contains the modules of the application and is able to choose a module
     * for a given request
     * @return ModuleManagerInterface
     */
    public function getModuleManager(): ModuleManagerInterface
    {
        if (!$this->moduleManager) {
            $this->moduleManager = new ApplicationModuleManager($this->resolvePath('modules'), $this);
        }

        return $this->moduleManager;
    }

    /**
     * Get the value of a named initialisation value passed during the construction of the application
     * @param string $value
     * @return null
     */
    public function getInitialisationValue(string $value)
    {
        return $this->initialisationValues[$value] ?? null;
    }

    /**
     * Routes the given http request
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Exception
     */
    public function routeHttpRequest(ServerRequestInterface $request): ResponseInterface
    {

        $module = $this->getModuleManager()
            ->getModuleForRequest($request);

        return $module->routeHttpRequest($request);
    }

    protected function registerErrorHandler()
    {
        $run     = new WhoopsRun;
        $run->register();
        if ($this->isDev()) {
            $run->pushHandler(new PrettyPageHandler);
        } else {
            $run->pushHandler(new HtmlSafeHandler($this));
        }
    }
}
