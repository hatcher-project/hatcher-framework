<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Hatcher\Application;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Yaml\Yaml;

return function (Application $application) {

    $cacheDir = $application->getCacheDirectory();
    $configFile = $application->resolvePath('config/config.yml');

    if ($cacheDir) {
        $cachePath = $cacheDir . '/__app/config.cache.php';
        $cacheFile = new ConfigCache($cachePath, $application->isDev());

        if ($cacheFile->isFresh()) {
            return new Config(unserialize(file_get_contents($cachePath)));
        }
    }

    try {
        $configData = Yaml::parse(file_get_contents($configFile));
    } catch (\Exception $e) {
        // Config might contain sensitive informations
        // Prevent YAML loader to give these data in production
        if ($application->isDev()) {
            throw $e;
        } else {
            throw new Exception(
                'Unable to load configuration file. Enable dev mode to see what is wrong'
            );
        }
    }

    if (!is_array($configData)) {
        throw new Exception('Config file should be parsed as an array. Using file: ' . $configFile);
    }

    $config = new Config($configData);

    if ($cacheDir) {
        $cacheFile->write(serialize($config->getData()), [new FileResource($configFile)]);
    }

    return $config;
};
