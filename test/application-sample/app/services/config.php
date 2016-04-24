<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Hatcher\Application;

return function (Application $app) {

    $configRaw = include $app->resolvePath('config.php');

    $config = new Config($configRaw);

    return $config;
};
