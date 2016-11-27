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
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run as WhoopsRun;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\ServerRequestFactory;
use Hatcher\ModuleManager\ModuleManagerInterface;
use Hatcher\ModuleManager\ApplicationModuleManager;

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
     * Application constructor.
     * @param string $directory
     * @param ClassLoader $classLoader
     * @param array $options list of application initialisation options
     * Possible values:
     * - dev: true to enable dev mode and profiling
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
     * @return string
     */
    public function getEnv()
    {
        return $this->env;
    }

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

    public function getModuleManager(): ModuleManagerInterface
    {
        if (!$this->moduleManager) {
            $this->moduleManager = new ApplicationModuleManager($this->resolvePath('modules'), $this);
        }

        return $this->moduleManager;
    }

    public function getInitialisationValue($value)
    {
        return $this->initialisationValues[$value] ?? null;
    }

    public function routeHttpRequest(ServerRequestInterface $request): ResponseInterface
    {
        $module = $this->getModuleManager()
            ->getModuleForRequest($request);

        return $module->dispatchRequest($request);
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
