<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Hatcher\ModuleManager\RegisteredModules;

$config = new Config([]);
$application = new Application($config, $GLOBALS["composer"], new \Hatcher\DI(), true);

$application->getDI()->set("moduleManager", function() use($application){

    $moduleManager = new RegisteredModules();
    $modulFront = new Module(
        "frontend",
        include __DIR__ . "/modules/frontend/module.php",
        $application,
        new Config([])
    );
    $moduleManager->registerModule($modulFront);

    return $moduleManager;

});


return $application;
