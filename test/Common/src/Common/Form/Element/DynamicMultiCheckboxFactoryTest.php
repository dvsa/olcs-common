<?php

namespace CommonTest\Form\Element;

use Common\Form\Element\DynamicMultiCheckboxFactory;

/**
 * Class DynamicMultiCheckboxFactoryTest
 * @package CommonTest\Form\Element
 */
class DynamicMultiCheckboxFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateService()
    {

        $mockSl = $this->getMock('\Zend\Form\FormElementManager');
        $mockSl->expects($this->any())->method('getServiceLocator')->willReturnSelf();

        $sut = new DynamicMultiCheckboxFactory();
        $service = $sut->createService($mockSl);

        $this->assertInstanceOf('\Common\Form\Element\DynamicMultiCheckbox', $service);
        $this->assertSame($mockSl, $service->getServiceLocator());
    }
}
