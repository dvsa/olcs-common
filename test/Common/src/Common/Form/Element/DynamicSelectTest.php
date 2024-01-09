<?php

namespace CommonTest\Form\Element;

use Common\Form\Element\DynamicSelect;
use Common\Service\Data\PluginManager;
use Common\Service\Data\RefData;
use Interop\Container\ContainerInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase as TestCase;

/**
 * Class DynamicSelectTest
 * @package CommonTest\Form\Element
 */
class DynamicSelectTest extends TestCase
{
    private $sut;
    private $mockRefDataService;

    public function setUp(): void
    {
        $this->mockRefDataService = $this->createMock(RefData::class);
        $pluginManager = m::mock(PluginManager::class);
        $pluginManager->shouldReceive('get')->with(RefData::class)->andReturn($this->mockRefDataService);

        $this->sut = new DynamicSelect($pluginManager, 'name', []);
    }

    public function testSetOptions()
    {
        $this->sut->setOptions(['context' => 'testing', 'use_groups'=>true, 'other_option'=>true, 'label' => 'Testing']);

        $this->assertEquals('testing', $this->sut->getContext());
        $this->assertTrue($this->sut->useGroups());
        $this->assertTrue($this->sut->otherOption());
        $this->assertEquals('Testing', $this->sut->getLabel());
    }

    public function testBcSetOptions()
    {
        $this->sut =  new DynamicSelect();
        $this->sut->setOptions(['category' => 'testing']);

        $this->assertEquals('testing', $this->sut->getContext());
    }

    public function testGetValueOptions()
    {
        $this->mockRefDataService
            ->expects($this->once())
            ->method('fetchListOptions')
            ->with($this->equalTo('category'), $this->equalTo(false))
            ->willReturn(['key'=>'value']);

        $this->sut->setDataService($this->mockRefDataService);
        $this->sut->setContext('category');

        $this->assertEquals(['key'=>'value'], $this->sut->getValueOptions());

        //check that the values are only fetched once
        $this->sut->getValueOptions();
    }

    public function testGetValueOptionsWithOtherOption()
    {
        $this->mockRefDataService
            ->expects($this->once())
            ->method('fetchListOptions')
            ->with($this->equalTo('category'), $this->equalTo(false))
            ->willReturn(['key'=>'value']);

        $this->sut->setOtherOption(true);
        $this->sut->setDataService($this->mockRefDataService);
        $this->sut->setContext('category');

        $this->assertEquals(['key'=>'value', 'other' => 'Other'], $this->sut->getValueOptions());

        //check that the values are only fetched once
        $this->sut->getValueOptions();
    }

    public function testGetValueOptionsWithExclude()
    {
        $this->mockRefDataService
            ->expects($this->once())
            ->method('fetchListOptions')
            ->with($this->equalTo('category'), $this->equalTo(false))
            ->willReturn(['key' => 'value', 'exclude' => 'me', 'one_more' => 'one more value']);

        $this->sut->setExclude(['exclude']);
        $this->sut->setDataService($this->mockRefDataService);
        $this->sut->setContext('category');

        $this->assertEquals(['key'=>'value', 'one_more' => 'one more value'], $this->sut->getValueOptions());

        //check that the values are only fetched once
        $this->sut->getValueOptions();
    }

    public function testGetValueOptionsWithEmptyOption()
    {
        $this->mockRefDataService
            ->expects($this->once())
            ->method('fetchListOptions')
            ->with($this->equalTo('category'), $this->equalTo(false))
            ->willReturn(['key'=>'value']);

        $this->sut->setOtherOption(false);
        $this->sut->setEmptyOption('choose one');
        $this->sut->setContext('category');

        $this->assertEquals(['key'=>'value'], $this->sut->getValueOptions());

        // empty option does not get returned from getValueOptions,
        // it's appended in the view helper - @see Laminas\Form\View\Helper\FormSelect::render
        $this->assertEquals('choose one', $this->sut->getEmptyOption());
    }

    /**
     * @param $value
     * @param $expected
     * @dataProvider provideSetValue
     */
    public function testSetValue($value, $expected, $multiple = false)
    {
        $this->sut->setAttribute('multiple', $multiple);
        $this->sut->setValue($value);

        $this->assertEquals($expected, $this->sut->getValue());
    }

    public function provideSetValue()
    {
        return [
            ['test', 'test'],
            [[0=>'test', 1=> 'test2'], [0=>'test', 1=> 'test2']],
            [['id'=>'test', 'desc' => 'Test Item'], 'test'],
            [[], null],
            [[['id'=>'test', 'desc' => 'Test Item'], [0 => 'test2']], ['test', [0 => 'test2']], true],
            [[['id'=>'test', 'desc' => 'Test Item'], ['id'=>'test2', 'desc' => 'Test Item']], ['test', 'test2'], true]
        ];
    }

    public function testGetDataServiceThrows()
    {
        $serviceName = 'testListService';

        $mockService = $this->createMock('\StdClass');

        $mockSl = $this->createMock(ContainerInterface::class);
        $mockSl->expects($this->once())->method('get')->with($this->equalTo($serviceName))->willReturn($mockService);

        $this->sut->setServiceLocator($mockSl);
        $this->sut->setOptions(['service_name'=>$serviceName]);

        $thrown = false;

        try {
            $this->sut->getDataService();
        } catch (\Exception $e) {
            if ('Class testListService does not implement \Common\Service\Data\ListDataInterface' == $e->getMessage()) {
                $thrown = true;
            }
        }

        $this->assertTrue($thrown, 'Expected exception not thrown or message incorrect');
    }

    public function testAddValueOption()
    {
        $original = [
            1 => 2,
            2 => 3
        ];

        $additional = [
            3 => 4
        ];

        $this->sut->setValueOptions($original);
        $this->sut->addValueOption($additional);

        $this->assertEquals($this->sut->getValueOptions(), array_merge($original, $additional));
    }

    public function testExtraOption()
    {
        $this->mockRefDataService->shouldReceive('fetchListOptions')->once()->andReturn(['foo' => 'bar']);

        $this->sut->setExtraOption(['an' => 'option']);

        $this->assertSame(['an' => 'option', 'foo' => 'bar'], $this->sut->getValueOptions());
    }

    public function testExtraSetOption()
    {
        $this->sut->setOptions(['extra_option' => ['an' => 'option']]);

        $this->sut->getExtraOption(['an' => 'option']);

        $this->assertSame(['an' => 'option'], $this->sut->getExtraOption());
    }
}
