<?php
/**
 * Class AggregateInterfaceTest
 *
 * @package CommonTest\Crud
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace CommonTest\Crud;

use PHPUnit_Framework_TestCase;

/**
 * Class AggregateInterfaceTest
 *
 * @package CommonTest\Crud
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class AggregateInterfaceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests the aggregate interface and in turn also tests the individual interfaces.
     */
    public function testAggregateInterfaceMethodsExist()
    {
        $interface = $this->getMock('Common\Crud\AggregateInterface');

        $this->assertTrue(method_exists($interface, 'create'));
        $this->assertTrue(method_exists($interface, 'get'));
        $this->assertTrue(method_exists($interface, 'getList'));
        $this->assertTrue(method_exists($interface, 'update'));
        $this->assertTrue(method_exists($interface, 'delete'));
    }
}
