<?php
/**
 * Table Builder Interface Test
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace CommonTest\Service\Table;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * Table Builder Interface Test
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class TableBuilderInterfaceTest extends TestCase
{
    /**
     * Tests the trait's get and set methods.
     */
    public function testSetGetTableBuilder()
    {
        /** @var \Common\Service\Table\TableBuilderAwareInterface $trait */
        $interface = $this->getMock('\Common\Service\Table\TableBuilderAwareInterface');

        $this->assertTrue(method_exists($interface, 'getTableBuilder'), 'Class does not have method getTableBuilder');
        $this->assertTrue(method_exists($interface, 'setTableBuilder'), 'Class does not have method setTableBuilder');
    }
}