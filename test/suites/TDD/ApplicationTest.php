<?php
/**
 * @license see LICENSE
 */
namespace Hatcher\Test\TDD;

use Composer\Autoload\ClassLoader;
use Hatcher\Application;
use Hatcher\Config;
use Hatcher\DI;

/**
 * @covers Hatcher\Application
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Application
     */
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



    public function testIsDev()
    {
        $this->assertTrue($this->application->isDev());
    }

    public function testGetClassLoader()
    {
        $this->assertInstanceOf(ClassLoader::class, $this->application->getClassLoader());
    }
}
