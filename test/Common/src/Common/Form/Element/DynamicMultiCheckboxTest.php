<?php

declare(strict_types=1);

namespace CommonTest\Form\Element;

use Common\Form\Element\DynamicMultiCheckbox;
use Common\Service\Data\PluginManager;
use Common\Service\Data\RefData;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class DynamicMultiCheckboxTest extends MockeryTestCase
{
    private $pluginManager;

    public function setUp(): void{
        $this->pluginManager = m::mock(PluginManager::class);
    }

    public function testSetOptions()
    {
        $sut =  new DynamicMultiCheckbox($this->pluginManager);
        $sut->setOptions(['context' => 'testing', 'use_groups'=>true, 'label' => 'Testing']);

        $this->assertEquals('testing', $sut->getContext());
        $this->assertTrue($sut->useGroups());
        $this->assertEquals('Testing', $sut->getLabel());
    }

    public function testBcSetOptions()
    {
        $sut =  new DynamicMultiCheckbox($this->pluginManager);
        $sut->setOptions(['category' => 'testing']);

        $this->assertEquals('testing', $sut->getContext());
    }

    public function testGetValueOptions()
    {
        $mockRefDataService = $this->createMock(RefData::class);
        $mockRefDataService
            ->expects($this->once())
            ->method('fetchListOptions')
            ->with($this->equalTo('category'), $this->equalTo(false))
            ->willReturn(['key'=>'value']);

        $sut = new DynamicMultiCheckbox($this->pluginManager);
        $sut->setDataService($mockRefDataService);
        $sut->setContext('category');

        $this->assertEquals(['key'=>'value'], $sut->getValueOptions());

        //check that the values are only fetched once
        $sut->getValueOptions();
    }

    /**
     * @param $value
     * @param $expected
     * @dataProvider provideSetValue
     */
    public function testSetValue($value, $expected)
    {
        $sut = new DynamicMultiCheckbox($this->pluginManager);
        $sut->setValue($value);

        $this->assertEquals($expected, $sut->getValue());
    }

    public function provideSetValue()
    {
        return [
            ['test', 'test'],
            [[0=>'test', 1=> 'test2'], [0=>'test', 1=> 'test2']],
            [['id'=>'test', 'desc' => 'Test Item'], 'test']
        ];
    }

    public function testGetDataService()
    {
        $serviceName = 'testListService';

        $mockService = $this->createMock(\Common\Service\Data\ListDataInterface::class);

        $this->pluginManager->expects('get')->with($serviceName)->andReturn($mockService);
        $sut =  new DynamicMultiCheckbox($this->pluginManager);
        $sut->setServiceName($serviceName);
        $this->assertEquals($mockService, $sut->getDataService());
    }

    public function testGetDataServiceThrows()
    {
        $serviceName = 'testListService';

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'Class ' . $serviceName . ' does not implement \Common\Service\Data\ListDataInterface'
        );

        $mockService = $this->createMock('\StdClass');

        $this->pluginManager->expects('get')->with($serviceName)->andReturn($mockService);
        $sut =  new DynamicMultiCheckbox($this->pluginManager);
        $sut->setOptions(['service_name' => $serviceName]);
        $sut->getDataService();
    }
}
