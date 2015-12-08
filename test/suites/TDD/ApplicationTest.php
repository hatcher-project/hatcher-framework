<?php
/**
 * @license see LICENSE
 */
namespace Hatcher\Test\TDD;

use Composer\Autoload\ClassLoader;
use Hatcher\Application;
use Hatcher\Config;
use Hatcher\DI;
use Hatcher\ModuleManager;

/**
 * @covers Hatcher\Application
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Application
     */
    protected $application;

    public function setUp()
    {


        $di = new DI();
        $di->set("foo", function () {
            return "bar";

        });


        $this->application = new Application(
            $GLOBALS['applicationSample'],
            $GLOBALS['composer'],
            [
                "dev" => true,
                "configFile" => "config.php",
                "configFormat" => "php"
            ]
        );

    }

    public function testGetConfig()
    {
        $this->assertInstanceOf(Config::class, $this->application->getConfig());
        $this->assertEquals("bar", $this->application->getConfig()->get("foo"));
    }


    public function testIsDev()
    {
        $this->assertTrue($this->application->isDev());
    }

    public function testGetClassLoader()
    {
        $this->assertInstanceOf(ClassLoader::class, $this->application->getClassLoader());
    }

    public function testGetModuleManager()
    {
        $this->assertInstanceOf(ModuleManager::class, $this->application->getModuleManager());
    }
}
