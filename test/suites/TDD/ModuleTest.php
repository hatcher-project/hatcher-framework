<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\Test\TDD;

use Composer\Autoload\ClassLoader;
use Hatcher\Application;
use Hatcher\Config;
use Hatcher\DI;
use Hatcher\Module;
use Hatcher\ModuleAdapter;

/**
 * @covers Hatcher\Module
 */
class ModuleTest extends \PHPUnit_Framework_TestCase
{

    protected $application;

    public function setUp()
    {
        $this->application = new Application(
            new Config(["foo" => "bar"]),
            new ClassLoader(),
            new DI(),
            true
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

    public function testGetApplication()
    {
        $module = $this->mockModule("A");
        $this->assertSame($this->application, $module->getApplication());
    }
}
