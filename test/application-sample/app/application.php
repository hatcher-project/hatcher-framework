<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

$application = new Application(
    $GLOBALS['applicationSample'],
    $GLOBALS['composer'],
    [
        "dev" => true,
        "configFile" => "config.php",
        "configFormat" => "php"
    ]
);

$application->registerModuleNames(
    "frontend"
);

return $application;
