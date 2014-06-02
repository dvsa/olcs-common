<?php

/**
 * Table Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace CommonTest\Service\Table;

use Common\Service\Table\TableFactory;

/**
 * Table Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TableFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test createService
     */
    public function testCreateService()
    {
        $serviceLocator = $this->getMock('\Zend\ServiceManager\ServiceManager', array('get'));

        $serviceLocator->expects($this->once())
            ->method('get')
            ->with('Config')
            ->will($this->returnValue(array()));

        $tableFactory = new TableFactory();

        $table = $tableFactory->createService($serviceLocator)->getTableBuilder();

        $this->assertTrue($table instanceof \Common\Service\Table\TableBuilder);
    }

    /**
     * Test buildTable
     */
    public function testBuildTable()
    {
        $name = 'foo';
        $data = array('foo' => 'var');
        $params = array('cake' => 'bbar');
        $render = true;

        $mockTable = $this->getMock('\stdClass', array('buildTable'));

        $mockTable->expects($this->once())
            ->method('buildTable')
            ->with($name, $data, $params, $render);

        $tableFactory = $this->getMock('\Common\Service\Table\TableFactory', array('getTableBuilder'));

        $tableFactory->expects($this->once())
            ->method('getTableBuilder')
            ->will($this->returnValue($mockTable));

        $tableFactory->buildTable($name, $data, $params, $render);
    }
}
