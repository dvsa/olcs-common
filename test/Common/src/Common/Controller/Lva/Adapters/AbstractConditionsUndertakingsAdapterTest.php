<?php

namespace CommonTest\Common\Controller\Lva\Adapters;

use Common\Controller\Lva\Adapters\AbstractConditionsUndertakingsAdapter;
use Common\RefData;
use Common\Service\Script\ScriptFactory;
use Interop\Container\ContainerInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class AbstractConditionsUndertakingsAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $container;

    public function setUp(): void
    {
        $this->container = m::mock(ContainerInterface::class);

        // Mock it as it is abstract
        $this->sut = m::mock(AbstractConditionsUndertakingsAdapter::class, [$this->container])
            ->makePartial()
            // We need to mock some unimplemented abstract methods
            ->shouldAllowMockingProtectedMethods();
    }

    public function testAttachMainScripts()
    {
        $mockScript = m::mock();

        $this->container->shouldReceive('get')->with(ScriptFactory::class)->andReturn($mockScript);

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
        $table = m::mock(\Common\Service\Table\TableBuilder::class);
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
