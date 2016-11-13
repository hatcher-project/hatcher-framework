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
        $di->set('foo', function () {
            return 'bar';
        });

        $this->application = new ApplicationSegment(
            $GLOBALS['applicationSample'],
            $di
        );
    }



    public function testGetDi()
    {
        $this->assertInstanceOf(DI::class, $this->application->getDI());
    }


    public function testMagicGet()
    {
        $this->assertEquals('bar', $this->application->foo);
    }


    public function testResolvePath()
    {
        $this->assertEquals($GLOBALS['applicationSample'], $this->application->resolvePath());
        $this->assertEquals($GLOBALS['applicationSample'] . '/bar', $this->application->resolvePath('bar'));
    }
}
