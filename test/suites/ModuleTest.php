<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\Test;

use Composer\Autoload\ClassLoader;
use Hatcher\Application;
use Hatcher\Config;
use Hatcher\DI;
use Hatcher\Module;

/**
 * @covers Hatcher\Module
 */
class ModuleTest extends \PHPUnit_Framework_TestCase
{

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

    public function testGetApplication()
    {
        $module = $this->mockModule();
        $this->assertSame($this->application, $module->getApplication());
    }
}
