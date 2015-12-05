<?php
/**
 * @license see LICENSE
 */

namespace Hatcher\Test;

use Hatcher\DI;

/**
 * @covers Hatcher\Di
 */
class DiTest extends \PHPUnit_Framework_TestCase
{

    public function testGetException()
    {
        $di = new DI();
        $this->setExpectedException("InvalidArgumentException");
        $di->get("something");
    }

    public function testGetAndSetService()
    {
        $di = new DI();
        $di->set("something", function () {
            $o = new \stdClass();
            $o->name = "something";
            return $o;
        });

        $initial = $di->get("something");
        $this->assertInstanceOf(\stdClass::class, $initial);
        $this->assertSame($initial, $di->get("something"));
    }
}
