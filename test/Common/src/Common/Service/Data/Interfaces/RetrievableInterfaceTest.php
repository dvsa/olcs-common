<?php

namespace CommonTest\Service\Data\Interfaces;

use PHPUnit_Framework_TestCase;

/**
* Class RetrievableInterfaceTest
*
* @package CommonTest\Service\Data
* @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
*/
class RetrievableInterfaceTest extends PHPUnit_Framework_TestCase
{
     /**
     * Tests the retrievable interface
     */
    public function testAggregateInterfaceMethodsExist()
    {
        $interface = $this->getMock('Common\Service\Data\Interfaces\Retrievable');

        $this->assertTrue(method_exists($interface, 'getCount'));
        $this->assertTrue(method_exists($interface, 'getResults'));
        $this->assertTrue(method_exists($interface, 'fetchList'));
    }
}
