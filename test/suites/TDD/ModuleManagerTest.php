<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\Test\TDD;

use Hatcher\Application;
use Hatcher\ModuleManager;
use Hatcher\Test\HatcherTestCase;

class ModuleManagerTest extends HatcherTestCase
{

    /**
     * @var ModuleManager
     */
    protected $moduleManager;

    /**
     * @var \Hatcher\Application
     */
    protected $application;

    public function setUp()
    {
        $this->application = $this->generateApplication();
        $this->moduleManager = $this->application->getModuleManager();
    }

    public function testGetModule()
    {
        $module = $this->moduleManager->getModule('frontend');
        $this->assertEquals('frontend', $module->getName());
    }
}
