<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\Test\TDD;

use Hatcher\Config;

/**
 * @covers Hatcher\Config\SimpleConfig
 * @covers Hatcher\Config\ConfigProcessor
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{

    public function testConfigGetter()
    {
        $config = new Config\SimpleConfig([
            'foo' => 'bar',
            'bar' => 'baz',
            'baz' => [
                'foo' => 'foobar'
            ]
        ]);

        $this->assertEquals('bar', $config->get('foo'));
        $this->assertEquals('baz', $config->get('bar'));
        $this->assertEquals('foobar', $config->get('baz.foo'));
        $this->assertEquals(['foo' => 'foobar'], $config->get('baz'));
    }

    public function testConfigProcessor()
    {
        $confFile = $GLOBALS['applicationSample'] . '/config/config.yml';

        $config = new Config\ConfigProcessor($confFile);

        $this->assertEquals([
            'something' => 'a thing',
            'foo' => [
                'bar' => 'this is bar',
                'baz' => 'this is the new baz',
                'qux' => 'fresher qux'

            ],
            'db'  => [
                'host' => 'hostname',
                'user' => 'user'

            ]
        ], $config->all());
    }
}
