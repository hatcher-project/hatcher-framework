<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\Test;

class HatcherTestCase extends \PHPUnit_Framework_TestCase
{
    protected function generateApplication()
    {
        $application = require $GLOBALS['applicationSample'] . '/../bootstrap.php';
        return $application;
    }
}
