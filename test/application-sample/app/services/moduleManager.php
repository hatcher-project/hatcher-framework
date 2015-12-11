<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Hatcher\Application;

return function(Application $app){
    $moduleManager = new ModuleManager($app->resolvePath("modules"), $app);
    $moduleManager->registerModule("frontend", $app->config->get("modules.front.matcher"));

    return $moduleManager;
};
