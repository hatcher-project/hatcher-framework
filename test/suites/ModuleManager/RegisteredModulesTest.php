<?php
/**
 * @license see LICENSE
 */
namespace Hatcher\Test\ModuleManager;

use Composer\Autoload\ClassLoader;
use Hatcher\Config;
use Hatcher\DI;
use Hatcher\Module;
use Hatcher\ModuleManager\RegisteredModules;
use Hatcher\ModuleManager\ModuleDoesNotExistException;
use Hatcher\Application;

/**
 * @covers Hatcher\ModuleManager\RegisteredModules
 */
class RegisteredModulesTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Application
     */
    protected $application;

    public function setUp()
    {
        $this->application = $this->getMockForAbstractClass(
            Application::class,
            [
                "./root",
                new Config(["foo" => "bar"]),
                new ClassLoader(),
                new DI(),
                true
            ]
        );
    }

    /**
     * @return Module
     */
    protected function mockModule()
    {
        return $this->getMockForAbstractClass(Module::class, [
            $this->application,
            "moduleRoot",
            new Config([])
        ]);
    }

    public function testGetModule()
    {
        $moduleManager = new RegisteredModules();

        $moduleA = $this->mockModule();

        $moduleManager->registerModule("A", $moduleA);
        $this->assertSame($moduleA, $moduleManager->getModule("A"));

        $this->setExpectedException(ModuleDoesNotExistException::class);
        $moduleManager->getModule("B");
    }


    public function testGetModules()
    {
        $moduleManager = new RegisteredModules();

        $moduleA = $this->mockModule();
        $moduleB = $this->mockModule();

        $moduleManager->registerModule("A", $moduleA);
        $moduleManager->registerModule("B", $moduleB);

        $this->assertSame($moduleA, $moduleManager->getModules()["A"]);
        $this->assertSame($moduleB, $moduleManager->getModules()["B"]);
    }
}
