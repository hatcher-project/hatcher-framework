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
            "root",
            new ClassLoader(),
            true
        );
    }


    /**
     * @return Module
     */
    protected function mockModule($name)
    {

        return $this->getMockForAbstractClass(Module::class, [
            $name,
            "root",
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
