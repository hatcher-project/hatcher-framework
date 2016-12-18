<?php
/**
 * @license see LICENSE
 */
namespace Hatcher\Test\TDD;

use Composer\Autoload\ClassLoader;
use Hatcher\Application;
use Hatcher\Config;
use Hatcher\ModuleManager;
use Hatcher\Test\HatcherTestCase;

/**
 * @covers Hatcher\Application
 */
class ApplicationTest extends HatcherTestCase
{

    /**
     * @var \Hatcher\Application
     */
    protected $application;

    public function setUp()
    {
        $this->application = $this->generateApplication();
    }

    public function testIsDev()
    {
        // Default to false
        $application = new Application('/dev/null', $GLOBALS['composer']);
        $this->assertFalse($application->isDev());

        // Explicit  setting
        $application = new Application('/dev/null', $GLOBALS['composer'], ['dev' => true]);
        $this->assertTrue($application->isDev());
        $application = new Application('/dev/null', $GLOBALS['composer'], ['dev' => false]);
        $this->assertFalse($application->isDev());

        // Test .env
        $this->assertTrue($this->application->isDev());
    }

    public function testGetClassLoader()
    {
        $this->assertInstanceOf(ClassLoader::class, $this->application->getClassLoader());
    }
}
