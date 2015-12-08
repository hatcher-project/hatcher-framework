<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\Test\TDD;


use Hatcher\Application;
use Hatcher\ModuleManager;

class ModuleManagerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ModuleManager
     */
    protected $moduleManager;

    /**
     * @var Application
     */
    protected $application;

    public function setUp(){
        $this->application = new Application(
            $GLOBALS['applicationSample'],
            $GLOBALS['composer'],
            [
                "dev" => true,
                "configFile" => "config.php",
                "configFormat" => "php"
            ]
        );

        $this->moduleManager = $this->application->getModuleManager();
    }

    public function testGetModule(){

        $module = $this->moduleManager->getModule("frontend");

        $this->assertEquals("frontend", $module->getName());

    }

}
