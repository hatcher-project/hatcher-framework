<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\Test\TDD;

use Composer\Autoload\ClassLoader;
use Hatcher\ApplicationSegment;
use Hatcher\Config;
use Hatcher\DI;
use Hatcher\ModuleManagerInterface;

/**
 * @covers Hatcher\ApplicationSegment
 */
class ApplicationSegmentTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ApplicationSegment
     */
    protected $application;

    public function setUp()
    {
        $this->application = new ApplicationSegment(
            new Config(["foo" => "bar"]),
            new DI()
        );
    }



    public function testGetDi()
    {
        $this->assertInstanceOf(DI::class, $this->application->getDI());
    }

    public function testGetConfig()
    {
        $this->assertInstanceOf(Config::class, $this->application->getConfig());
        $this->assertEquals("bar", $this->application->getConfig()->get("foo"));
    }
}
