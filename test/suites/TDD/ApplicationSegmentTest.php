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
        $di = new DI();
        $di->set("foo", function(){return "bar";});

        $this->application = new ApplicationSegment(
            $GLOBALS['applicationSample'],
            $di,
            new Config\ConfigFactory($GLOBALS["applicationSample"] . "/config.php", "php", null)
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

    public function  test__get(){
        $this->assertEquals("bar", $this->application->foo);
        $this->assertInstanceOf(Config::class, $this->application->config);
    }


    public function testResolvePath(){
        $this->assertEquals($GLOBALS["applicationSample"], $this->application->resolvePath());
        $this->assertEquals($GLOBALS["applicationSample"] . "/bar", $this->application->resolvePath("bar"));
    }
}
