<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Hatcher\Application;
use Hatcher\Config\ConfigProcessor;
use Hatcher\Config\SimpleConfig;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Yaml\Yaml;

return function (Application $application) {

    $cacheDir = $application->getCacheDirectory();

    if ($cacheDir) {
        $cachePath = $cacheDir . '/__app/config.cache.php';
        $cacheFile = new ConfigCache($cachePath, $application->isDev());

        if ($cacheFile->isFresh()) {
            return new SimpleConfig(unserialize(file_get_contents($cachePath)));
        }
    }


    $configFile = getenv('CONFIG_FILE');
    $configFile = $application->resolvePath($configFile ? $configFile : 'config/config.yml');
    $config = new ConfigProcessor($configFile);

    if ($cacheDir) {
        $cacheFile->write(serialize($config->all()), [new FileResource($configFile)]);
    }

    return $config;
};
