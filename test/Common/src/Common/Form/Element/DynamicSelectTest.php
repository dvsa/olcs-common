<?php

namespace CommonTest\Form\Element;

use Common\Form\Element\DynamicSelect;

/**
 * Class DynamicSelectTest
 * @package CommonTest\Form\Element
 */
class DynamicSelectTest extends \PHPUnit_Framework_TestCase
{
    public function testSetOptions()
    {
        $sut =  new DynamicSelect();
        $sut->setOptions(['context' => 'testing', 'use_groups'=>true, 'other_option'=>true, 'label' => 'Testing']);

        $this->assertEquals('testing', $sut->getContext());
        $this->assertTrue($sut->useGroups());
        $this->assertTrue($sut->otherOption());
        $this->assertEquals('Testing', $sut->getLabel());
    }

    public function testBcSetOptions()
    {
        $sut =  new DynamicSelect();
        $sut->setOptions(['category' => 'testing']);

        $this->assertEquals('testing', $sut->getContext());
    }

    public function testGetValueOptions()
    {
        $mockRefDataService = $this->getMock('\Common\Service\Data\RefData');
        $mockRefDataService
            ->expects($this->once())
            ->method('fetchListOptions')
            ->with($this->equalTo('category'), $this->equalTo(false))
            ->willReturn(['key'=>'value']);

        $sut = new DynamicSelect();
        $sut->setDataService($mockRefDataService);
        $sut->setContext('category');

        $this->assertEquals(['key'=>'value'], $sut->getValueOptions());

        //check that the values are only fetched once
        $sut->getValueOptions();
    }

    public function testGetValueOptionsWithOtherOption()
    {
        $mockRefDataService = $this->getMock('\Common\Service\Data\RefData');
        $mockRefDataService
            ->expects($this->once())
            ->method('fetchListOptions')
            ->with($this->equalTo('category'), $this->equalTo(false))
            ->willReturn(['key'=>'value']);

        $sut = new DynamicSelect();
        $sut->setOtherOption(true);
        $sut->setDataService($mockRefDataService);
        $sut->setContext('category');

        $this->assertEquals(['key'=>'value', 'other' => 'Other'], $sut->getValueOptions());

        //check that the values are only fetched once
        $sut->getValueOptions();
    }

    public function testGetValueOptionsWithEmptyOption()
    {
        $mockRefDataService = $this->getMock('\Common\Service\Data\RefData');
        $mockRefDataService
            ->expects($this->once())
            ->method('fetchListOptions')
            ->with($this->equalTo('category'), $this->equalTo(false))
            ->willReturn(['key'=>'value']);

        $sut = new DynamicSelect();
        $sut->setOtherOption(false);
        $sut->setEmptyOption('choose one');
        $sut->setDataService($mockRefDataService);
        $sut->setContext('category');

        $this->assertEquals(
            ['' => 'choose one', 'key'=>'value'],
            $sut->getValueOptions()
        );
    }

    /**
     * @param $value
     * @param $expected
     * @dataProvider provideSetValue
     */
    public function testSetValue($value, $expected, $multiple = false)
    {
        $sut = new DynamicSelect();
        $sut->setAttribute('multiple', $multiple);
        $sut->setValue($value);

        $this->assertEquals($expected, $sut->getValue());
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

    public function testGetDataService()
    {
        $serviceName = 'testListService';

        $mockService = $this->getMock('\Common\Service\Data\ListDataInterface');

        $mockSl = $this->getMock('\Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->expects($this->once())->method('get')->with($this->equalTo($serviceName))->willReturn($mockService);

        $sut = new DynamicSelect();
        $sut->setServiceLocator($mockSl);
        $sut->setServiceName($serviceName);
        $this->assertEquals($mockService, $sut->getDataService());
    }

    public function testGetDataServiceThrows()
    {
        $serviceName = 'testListService';

        $mockService = $this->getMock('\StdClass');

        $mockSl = $this->getMock('\Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->expects($this->once())->method('get')->with($this->equalTo($serviceName))->willReturn($mockService);

        $sut = new DynamicSelect();
        $sut->setServiceLocator($mockSl);
        $sut->setOptions(['service_name'=>$serviceName]);

        $thrown = false;

        try {
            $sut->getDataService();
        } catch (\Exception $e) {
            if ('Class testListService does not implement \Common\Service\Data\ListDataInterface' == $e->getMessage()) {
                $thrown = true;
            }
        }

        $this->assertTrue($thrown, 'Expected exception not thrown or message incorrect');
    }
}
