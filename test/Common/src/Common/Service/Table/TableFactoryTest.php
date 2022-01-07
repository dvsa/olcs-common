<?php

namespace CommonTest\Service\Table;

use Common\Service\Table\TableBuilder;
use Common\Service\Table\TableFactory;
use Dvsa\Olcs\Utils\Translation\TranslatorDelegator;
use Laminas\ServiceManager\ServiceManager;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

/**
 * Table Factory Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TableFactoryTest extends MockeryTestCase
{
    /**
     * Test createService
     */
    public function testCreateService()
    {
        $mockAuthService = m::mock(AuthorizationService::class);
        $mockTranslator = m::mock(TranslatorDelegator::class);

        $serviceLocator = m::mock(ServiceManager::class);
        $serviceLocator->expects('get')->with('Config')->andReturn([]);
        $serviceLocator->expects('get')->with(AuthorizationService::class)->andReturn($mockAuthService);
        $serviceLocator->expects('get')->with('translator')->andReturn($mockTranslator);

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
