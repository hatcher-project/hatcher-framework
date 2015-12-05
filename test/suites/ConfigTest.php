<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\Test;

use Hatcher\Config;

/**
 * @covers Config
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{

    public function testConfigGetter()
    {
        $config = new Config([
            'foo' => 'bar',
            'bar' => 'baz',
            'baz' => [
                "foo" => "foobar"
            ]
        ]);

        $this->assertEquals("bar", $config->get("foo"));
        $this->assertEquals("baz", $config->get("bar"));
        $this->assertEquals("foobar", $config->get("baz.foo"));
        $this->assertEquals(["foo" => "foobar"], $config->get("baz"));

    }
}
