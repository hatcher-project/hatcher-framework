<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\DefaultApplication\Application\Service;

use Hatcher\Application;
use Hatcher\Config\ConfigProcessor;
use Hatcher\Config\SimpleConfig;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Resource\FileResource;

class Config
{

    public function __invoke(Application $application)
    {
        $cacheDir = $application->getCacheDirectory();

        $cachePath = $cacheDir . '/__app/config.cache.php';
        $cacheFile = new ConfigCache($cachePath, $application->isDev());

        if ($cacheFile->isFresh()) {
            return new SimpleConfig(unserialize(file_get_contents($cachePath)));
        }


        $configFile = $application->resolvePath('config/config.yml');
        $config = new ConfigProcessor($configFile);

        $cacheFile->write(serialize($config->all()), [new FileResource($configFile)]);

        return $config;
    }
}
