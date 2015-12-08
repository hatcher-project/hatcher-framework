<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use \Composer\Autoload\ClassLoader;
use Hatcher\Application\DefaultOptions;
use Hatcher\Config\ConfigFactory;

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

    public function __construct(string $directory, ClassLoader $classLoader, array $options = []) {

        $di = new DirectoryDi($options["servicesDirectory"] ?? "services");

        $configFactory = new ConfigFactory(
            $directory . "/" . $options["configFile"] ?? "config.yaml",
            $options["configFormat"] ?? "yaml",
            $options["cache"] ?? null
        );

        parent::__construct($directory, $di, $configFactory);
        $this->dev = (bool) $options["dev"] ?? true;
        $this->classLoader = $classLoader;

        $this->moduleManager = new ModuleManager($this->resolvePath($options["moduleDirectory"] ?? "modules"), $this);
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

    public function getModuleManager(){
        return $this->moduleManager;
    }
}
