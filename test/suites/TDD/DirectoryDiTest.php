<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\Test\TDD;

use Hatcher\DirectoryDi;

/**
 * @covers Hatcher\DirectoryDi
 */
class DirectoryDiTest extends \PHPUnit_Framework_TestCase
{

    public function testGetService()
    {
        $di = new DirectoryDi($GLOBALS['applicationSample'] . '/services');
        $this->assertEquals('fooService', $di->get('foo'));
    }
}
