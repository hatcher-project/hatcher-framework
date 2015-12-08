<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use \Composer\Autoload\ClassLoader;
use Hatcher\Application\DefaultOptions;
use Hatcher\Config\ConfigFactory;
use Psr\Http\Message\ServerRequestInterface;

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

        $di = new DirectoryDi($directory . "/services");

        $configFactory = new ConfigFactory(
            $directory . "/" . ($options["configFile"] ?? "config.yaml"),
            $options["configFormat"] ?? "yaml",
            $options["cache"] ?? null
        );

        parent::__construct($directory, $di, $configFactory);
        $this->dev = (bool)($options["dev"] ?? true);
        $this->classLoader = $classLoader;

        $this->moduleManager = new ModuleManager($this->resolvePath($options["moduleDirectory"] ?? "modules"), $this);
    }

    public function registerModuleNames(string ...$names)
    {
        $this->moduleManager->registerModuleNames($names);
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

    public function getModuleManager()
    {
        return $this->moduleManager;
    }

    public function routeHttpRequest(ServerRequestInterface $request)
    {
        foreach ($this->moduleManager->getModuleNames() as $moduleName) {
            $module = $this->moduleManager->getModule($moduleName);
            if ($this->moduleManager->getModule($moduleName)->requestMatches($request)) {
                return $module->dispatchRequest($request);
            }
        }

        throw new Exception("No module matched the request");
    }
}
