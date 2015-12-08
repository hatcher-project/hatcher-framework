<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\Test;

use Hatcher\Application;

class HatcherTestCase extends \PHPUnit_Framework_TestCase
{

    protected function generateApplication()
    {
        $application = new Application(
            $GLOBALS['applicationSample'],
            $GLOBALS['composer'],
            [
                "dev" => true,
                "configFile" => "config.php",
                "configFormat" => "php"
            ]
        );

        $application->registerModuleNames("frontend");

        return $application;
    }
}
