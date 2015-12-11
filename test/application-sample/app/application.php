<?php
/**
 * @license see LICENSE
 */

namespace Hatcher;

use Hatcher\Application;

$application = new Application(
    $GLOBALS['applicationSample'],
    $GLOBALS['composer'],
    [
        "dev" => true
    ]
);

return $application;
