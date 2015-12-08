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
use Hatcher\Test\HatcherTestCase;

/**
 * @covers Hatcher\Application
 */
class ApplicationTest extends HatcherTestCase
{

    /**
     * @var Application
     */
    protected $application;

    public function setUp()
    {
        $this->application = $this->generateApplication();
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
