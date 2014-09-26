<?php

namespace CommonTest\Form\Element;

use Common\Form\Element\DynamicMultiCheckbox;

/**
 * Class DynamicMultiCheckboxTest
 * @package CommonTest\Form\Element
 */
class DynamicMultiCheckboxTest extends \PHPUnit_Framework_TestCase
{
    public function testSetOptions()
    {
        $sut =  new DynamicMultiCheckbox();
        $sut->setOptions(['context' => 'testing', 'use_groups'=>true, 'label' => 'Testing']);

        $this->assertEquals('testing', $sut->getContext());
        $this->assertTrue($sut->useGroups());
        $this->assertEquals('Testing', $sut->getLabel());
    }

    public function testBcSetOptions()
    {
        $sut =  new DynamicMultiCheckbox();
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

        $sut = new DynamicMultiCheckbox();
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
        $sut = new DynamicMultiCheckbox();
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

        $mockService = $this->getMock('\Common\Service\Data\ListDataInterface');

        $mockSl = $this->getMock('\Zend\ServiceManager\ServiceLocatorInterface');
        $mockSl->expects($this->once())->method('get')->with($this->equalTo($serviceName))->willReturn($mockService);

        $sut = new DynamicMultiCheckbox();
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

        $sut = new DynamicMultiCheckbox();
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
