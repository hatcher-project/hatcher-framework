<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

return function (Application $application) {
    $moduleManager = $application->getModuleManager();
    $moduleManager->registerModule('frontend', moduleMatchesHost('front.hatcher.test', '127.0.0.1'));
};
