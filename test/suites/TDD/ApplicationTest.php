<?php
/**
 * @license see LICENSE
 */
namespace Hatcher\Test\TDD;

use Composer\Autoload\ClassLoader;
use Hatcher\Application;
use Hatcher\Config;
use Hatcher\DI;
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
        $this->assertFalse($this->application->isDev());
    }

    public function testGetClassLoader()
    {
        $this->assertInstanceOf(ClassLoader::class, $this->application->getClassLoader());
    }
}
