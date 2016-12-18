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
        $application = new Application('/dev/null');
        $this->assertFalse($application->isDev());

        // Explicit  setting
        $application = new Application('/dev/null', ['dev' => true]);
        $this->assertTrue($application->isDev());
        $application = new Application('/dev/null', ['dev' => false]);
        $this->assertFalse($application->isDev());

        // Test .env
        $this->assertTrue($this->application->isDev());
    }
}
