<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\Test;

use Hatcher\Application;

class HatcherTestCase extends \PHPUnit_Framework_TestCase
{
    protected function generateApplication()
    {
        $application = require $GLOBALS['applicationSample'] . '/../bootstrap.php';
        return $application;
    }
}
