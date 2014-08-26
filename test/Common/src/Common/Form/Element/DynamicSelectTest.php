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
        $sut->setOptions(['category' => 'testing', 'use_groups'=>true, 'label' => 'Testing']);

        $this->assertEquals('testing', $sut->getCategory());
        $this->assertTrue($sut->useGroups());
        $this->assertEquals('Testing', $sut->getLabel());
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
        $sut->setRefDataService($mockRefDataService);
        $sut->setCategory('category');

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
        $sut = new DynamicSelect();
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
}
