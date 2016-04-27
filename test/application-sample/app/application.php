<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

return function (Application $application) {
    $moduleManager = $application->getModuleManager();
    $moduleManager->registerModule('frontend', $application->config->get('modules.front.matcher'));
};
