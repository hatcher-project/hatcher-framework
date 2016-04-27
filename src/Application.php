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

    /**
     * @var ModuleManager
     */
    protected $moduleManager;


    public function __construct(string $directory, ClassLoader $classLoader, array $options = [])
    {
        $di = new DirectoryDi($directory . '/services', [$this]);
        parent::__construct($directory, $di);

        $this->dev = (bool)($options['dev'] ?? false);
        $this->classLoader = $classLoader;

        if ($this->isDev()) {
            $this->registerErrorHandler();
        }

        call_user_func(require $this->resolvePath('application.php'), $this);
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
