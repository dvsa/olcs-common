<?php

/**
 * Abstract Conditions Undertakings Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Adapters;

use Common\RefData;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;

/**
 * Abstract Conditions Undertakings Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AbstractConditionsUndertakingsAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp(): void
    {
        $this->sm = Bootstrap::getServiceManager();

        // Mock it as it is abstract
        $this->sut = m::mock('\Common\Controller\Lva\Adapters\AbstractConditionsUndertakingsAdapter')
            ->makePartial()
            // We need to mock some unimplemented abstract methods
            ->shouldAllowMockingProtectedMethods();

        $this->sut->setServiceLocator($this->sm);
    }

    public function testAttachMainScripts()
    {
        $mockScript = m::mock();
        $this->sm->setService('Script', $mockScript);

        $mockScript->shouldReceive('loadFile')
            ->with('lva-crud');

        $this->sut->attachMainScripts();
    }

    public function testCanEditRecord()
    {
        $this->assertTrue($this->sut->canEditRecord(1, 2));
    }

    public function testGetTableName()
    {
        $this->assertEquals('lva-conditions-undertakings', $this->sut->getTableName());
    }

    public function testAlterTable()
    {
        $table = m::mock('\Common\Service\Table\TableBuilder');
        $table->shouldReceive('removeAction')->with('restore');

        $this->sut->alterTable($table);
    }

    public function testProcessDataForSave()
    {
        $id = 123;
        $data = [
            'fields' => [
                'attachedTo' => RefData::ATTACHED_TO_LICENCE
            ]
        ];
        $expected = [
            'fields' => [
                'attachedTo' => RefData::ATTACHED_TO_LICENCE,
                'operatingCentre' => null
            ]
        ];

        $return = $this->sut->processDataForSave($data, $id);

        $this->assertEquals($expected, $return);
    }

    public function testProcessDataForSaveWithOperatingCentre()
    {
        $id = 123;
        $data = [
            'fields' => [
                'attachedTo' => 'foo'
            ]
        ];
        $expected = [
            'fields' => [
                'attachedTo' => RefData::ATTACHED_TO_OPERATING_CENTRE,
                'operatingCentre' => 'foo'
            ]
        ];

        $return = $this->sut->processDataForSave($data, $id);

        $this->assertEquals($expected, $return);
    }
}
