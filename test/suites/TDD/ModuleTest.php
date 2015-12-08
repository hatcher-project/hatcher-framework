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

    /**
     * @var Application
     */
    protected $application;


    /**
     * @var Module
     */
    protected $module;

    public function setUp()
    {
        $this->application = new Application(
            $GLOBALS["applicationSample"],
            new ClassLoader(),
            []
        );
        $this->module = new Module("frontend", $this->application->resolvePath("modules/frontend"), $this->application);
    }


    public function testGetApplication()
    {
        $this->assertSame($this->application, $this->module->getApplication());
    }
}
