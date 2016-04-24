<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Hatcher\Application;

$application = new Application(
    __DIR__,
    $GLOBALS['composer'],
    [
        "dev" => false
    ]
);

$moduleManager = $application->getModuleManager();
$moduleManager->registerModule("frontend", $application->config->get("modules.front.matcher"));

return $application;
