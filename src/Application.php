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
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run as WhoopsRun;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\ServerRequestFactory;

/**
 * @property Config $config
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

    protected $cacheDirectory;

    /**
     * @var ModuleManager
     */
    protected $moduleManager;


    public function __construct(string $directory, ClassLoader $classLoader, bool $dev = false)
    {
        $this->dev = $dev;
        $this->classLoader = $classLoader;

        $di = new DirectoryDi($directory . '/services', [$this]);
        parent::__construct($directory, $di);

        $applicationInit = require $this->resolvePath('application.php');

        if (is_array($applicationInit)) {
            if (isset($applicationInit['modules']) && is_array($applicationInit['modules'])) {
                foreach ($applicationInit['modules'] as $moduleName => $moduleDef) {
                    $this->getModuleManager()->registerModule($moduleName, $moduleDef['matcher']);
                }
            }

            if (isset($applicationInit['cache-directory'])) {
                if (is_string($applicationInit['cache-directory'])) {
                    $this->cacheDirectory = $this->resolvePath($applicationInit['cache-directory']);
                } else {
                    throw new Exception(
                        'Invalid cache-directory option. cache-directory must be a string in '
                        . $this->resolvePath('application.php')
                    );
                }
            }
        } elseif (is_callable($applicationInit)) {
            call_user_func($applicationInit, $this);
        } else {
            throw new Exception('Application initialisation file is not valid');
        }




        if ($this->isDev()) {
            $this->registerErrorHandler();
        }
    }

    public function getCacheDirectory()
    {
        return $this->cacheDirectory;
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
    public function getClassLoader(): ClassLoader
    {
        return $this->classLoader;
    }

    public function getModuleManager(): ModuleManager
    {
        if (!$this->moduleManager) {
            $this->moduleManager = new ModuleManager($this->resolvePath('modules'), $this);
        }

        return $this->moduleManager;
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
        $handler = new PrettyPageHandler;
        $run->pushHandler($handler);
        $run->register();
    }
}
