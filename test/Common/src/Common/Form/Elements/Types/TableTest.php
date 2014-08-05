<?php

/**
 * Test Table Element
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Form\Elements\Types;

use PHPUnit_Framework_TestCase;
use Common\Form\Elements\Types\Table;

/**
 * Test Table Element
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TableTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test setTable
     */
    public function testSetTable()
    {
        $this->markTestSkipped('Does nothing of use');

        $fieldset = 'table';

        $mockTable = $this->getMock('\stdClass', array('setFieldset'));

        $mockTable->expects($this->once())
            ->method('setFieldset')
            ->with($fieldset);

        $table = new Table($fieldset);

        $table->setTable($mockTable);
    }

    /**
     * Test render
     */
    public function testRenderDefersToSuppliedTableRenderMethod()
    {
        $fieldset = 'table';

        $mockTable = $this->getMock('\stdClass', array('setFieldset', 'setDisabled', 'render'));

        $mockTable->expects($this->once())
            ->method('setFieldset')
            ->with($fieldset);

        $mockTable->expects($this->once())
            ->method('render')
            ->will($this->returnValue('<table></table>'));

        $table = new Table($fieldset);

        $table->setTable($mockTable);

        $this->assertEquals('<table></table>', $table->render());
    }
}
