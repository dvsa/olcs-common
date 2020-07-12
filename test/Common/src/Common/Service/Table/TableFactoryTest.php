<?php

/**
 * Table Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace CommonTest\Service\Table;

use Common\Service\Table\TableBuilder;
use Common\Service\Table\TableFactory;

/**
 * Table Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TableFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test createService
     */
    public function testCreateService()
    {
        $serviceLocator = $this->createPartialMock('\Zend\ServiceManager\ServiceManager', array('get'));

        $serviceLocator->expects($this->at(0))
            ->method('get')
            ->with('Config')
            ->will($this->returnValue(array()));

        $mockAuthService = $this->createMock('stdClass');

        $serviceLocator->expects($this->at(1))
            ->method('get')
            ->with('ZfcRbac\Service\AuthorizationService')
            ->will($this->returnValue($mockAuthService));

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

        $mockTable = $this->createPartialMock(TableBuilder::class, array('buildTable'));

        $mockTable->expects($this->once())
            ->method('buildTable')
            ->with($name, $data, $params, $render);

        $tableFactory = $this->createPartialMock('\Common\Service\Table\TableFactory', array('getTableBuilder'));

        $tableFactory->expects($this->once())
            ->method('getTableBuilder')
            ->will($this->returnValue($mockTable));

        $tableFactory->buildTable($name, $data, $params, $render);
    }
}
