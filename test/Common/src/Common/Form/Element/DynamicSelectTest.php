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
        $mockRefDataService = $this->getMock('\Common\Service\RefData');
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
}
