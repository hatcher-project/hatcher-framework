<?php
/**
 * @license see LICENSE
 */
namespace Hatcher\Test\TDD\ModuleManager;

use Composer\Autoload\ClassLoader;
use Hatcher\Config;
use Hatcher\DI;
use Hatcher\Module;
use Hatcher\ModuleAdapter;
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
    protected function mockModule($name)
    {

        $moduleAdapter = $this->getMockForAbstractClass(ModuleAdapter::class);

        return $this->getMockForAbstractClass(Module::class, [
            $name,
            $moduleAdapter,
            $this->application,
            new Config([])
        ]);
    }

    public function testGetModule()
    {
        $moduleManager = new RegisteredModules();

        $moduleA = $this->mockModule("A");

        $moduleManager->registerModule($moduleA);
        $this->assertSame($moduleA, $moduleManager->getModule("A"));

        $this->setExpectedException(ModuleDoesNotExistException::class);
        $moduleManager->getModule("B");
    }


    public function testGetModules()
    {
        $moduleManager = new RegisteredModules();

        $moduleA = $this->mockModule("A");
        $moduleB = $this->mockModule("B");

        $moduleManager->registerModule($moduleA);
        $moduleManager->registerModule($moduleB);

        $this->assertSame($moduleA, $moduleManager->getModules()["A"]);
        $this->assertSame($moduleB, $moduleManager->getModules()["B"]);
    }
}
